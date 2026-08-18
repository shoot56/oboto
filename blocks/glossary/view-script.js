/**
 * Glossary block: client-side search, category filters and alphabet navigation.
 *
 * All terms are rendered server-side, so filtering is a pure DOM operation and
 * stays instant regardless of the number of entries.
 */
(function () {
  "use strict";

  var SEARCH_DEBOUNCE_MS = 150;
  var MAX_SUGGESTIONS = 6;
  var READY_FLAG = "glossaryReady";

  function toArray(nodeList) {
    return Array.prototype.slice.call(nodeList || []);
  }

  function debounce(fn, wait) {
    var timer;
    return function () {
      var args = arguments;
      clearTimeout(timer);
      timer = setTimeout(function () {
        fn.apply(null, args);
      }, wait);
    };
  }

  function format(template, values) {
    var index = 0;
    return String(template)
      .replace(/%(\d)\$s/g, function (match, position) {
        return values[Number(position) - 1];
      })
      .replace(/%s/g, function () {
        return values[index++];
      });
  }

  function escapeHtml(value) {
    return String(value)
      .replace(/&/g, "&amp;")
      .replace(/</g, "&lt;")
      .replace(/>/g, "&gt;")
      .replace(/"/g, "&quot;");
  }

  /**
   * The theme header becomes sticky on scroll, so the alphabet bar has to sit
   * right below it. Measure the header instead of hard-coding its height.
   */
  function syncStickyOffset(container) {
    var header = document.querySelector(".site-header");
    var offset = 0;

    if (header) {
      var position = window.getComputedStyle(header).position;

      if (position === "sticky" || position === "fixed") {
        offset = Math.round(header.getBoundingClientRect().height);
      }
    }

    container.style.setProperty("--glossary-sticky-offset", offset + "px");
  }

  function watchStickyOffset(container) {
    var update = function () {
      syncStickyOffset(container);
    };

    update();
    window.addEventListener("resize", debounce(update, 150));

    var header = document.querySelector(".site-header");

    if (header && typeof MutationObserver !== "undefined") {
      new MutationObserver(update).observe(header, {
        attributes: true,
        attributeFilter: ["class", "style"]
      });
    }
  }

  function createGlossary(container) {
    var searchInput = container.querySelector("[data-glossary-search]");
    var suggestions = container.querySelector("[data-glossary-suggestions]");
    var chips = toArray(container.querySelectorAll("[data-glossary-group]"));
    var letterButtons = toArray(container.querySelectorAll("[data-glossary-letter]"));
    var sections = toArray(container.querySelectorAll("[data-glossary-letter-section]"));
    var terms = toArray(container.querySelectorAll("[data-glossary-term]"));
    var countEl = container.querySelector("[data-glossary-count]");
    var emptyEl = container.querySelector("[data-glossary-empty]");

    if (!terms.length) {
      return;
    }

    var total = terms.length;
    var activeGroup = "all";
    var activeQuery = "";
    var highlightedSuggestion = -1;

    function matchesTerm(term) {
      if (activeGroup !== "all" && term.getAttribute("data-glossary-group") !== activeGroup) {
        return false;
      }

      if (!activeQuery) {
        return true;
      }

      return (term.getAttribute("data-glossary-search") || "").indexOf(activeQuery) !== -1;
    }

    function updateCount(visible) {
      if (!countEl) {
        return;
      }

      var labelAll = countEl.getAttribute("data-label-all") || "%s terms";
      var labelFiltered = countEl.getAttribute("data-label-filtered") || "Showing %1$s of %2$s terms";

      countEl.textContent =
        visible === total
          ? format(labelAll, [String(total)])
          : format(labelFiltered, [String(visible), String(total)]);
    }

    function applyFilters() {
      var visible = 0;

      terms.forEach(function (term) {
        var show = matchesTerm(term);
        term.classList.toggle("is-hidden", !show);
        if (show) {
          visible += 1;
        }
      });

      var lettersWithResults = {};

      sections.forEach(function (section) {
        var hasVisible = section.querySelector("[data-glossary-term]:not(.is-hidden)") !== null;
        section.classList.toggle("is-hidden", !hasVisible);
        if (hasVisible) {
          lettersWithResults[section.getAttribute("data-glossary-letter-section")] = true;
        }
      });

      letterButtons.forEach(function (button) {
        var enabled = lettersWithResults[button.getAttribute("data-glossary-letter")] === true;
        button.disabled = !enabled;
        button.classList.toggle("is-disabled", !enabled);
        if (!enabled) {
          button.classList.remove("is-active");
        }
      });

      if (emptyEl) {
        emptyEl.hidden = visible !== 0;
      }

      updateCount(visible);
    }

    function closeSuggestions() {
      if (!suggestions) {
        return;
      }

      suggestions.hidden = true;
      suggestions.innerHTML = "";
      highlightedSuggestion = -1;

      if (searchInput) {
        searchInput.setAttribute("aria-expanded", "false");
      }
    }

    function renderSuggestions() {
      if (!suggestions || !searchInput) {
        return;
      }

      if (!activeQuery) {
        closeSuggestions();
        return;
      }

      var matches = terms
        .filter(function (term) {
          return (term.getAttribute("data-glossary-search") || "").indexOf(activeQuery) !== -1;
        })
        .sort(function (first, second) {
          var firstName = (first.getAttribute("data-glossary-name") || "").toLowerCase();
          var secondName = (second.getAttribute("data-glossary-name") || "").toLowerCase();
          var firstStarts = firstName.indexOf(activeQuery) === 0 ? 0 : 1;
          var secondStarts = secondName.indexOf(activeQuery) === 0 ? 0 : 1;

          if (firstStarts !== secondStarts) {
            return firstStarts - secondStarts;
          }

          return firstName.localeCompare(secondName);
        })
        .slice(0, MAX_SUGGESTIONS);

      if (!matches.length) {
        var emptyLabel = suggestions.getAttribute("data-empty-label") || "No matching terms";
        suggestions.innerHTML =
          '<p class="glossary__suggestion glossary__suggestion--empty">' + escapeHtml(emptyLabel) + "</p>";
        suggestions.hidden = false;
        searchInput.setAttribute("aria-expanded", "true");
        return;
      }

      suggestions.innerHTML = matches
        .map(function (term) {
          var name = term.getAttribute("data-glossary-name") || "";
          var description = term.querySelector(".glossary__term-desc");
          var href = term.tagName === "A" ? term.getAttribute("href") : "#" + term.id;

          return (
            '<a class="glossary__suggestion" role="option" href="' +
            escapeHtml(href) +
            '" data-glossary-suggestion="' +
            escapeHtml(term.id) +
            '"><strong>' +
            escapeHtml(name) +
            "</strong>" +
            (description ? "<small>" + escapeHtml(description.textContent) + "</small>" : "") +
            "</a>"
          );
        })
        .join("");

      suggestions.hidden = false;
      searchInput.setAttribute("aria-expanded", "true");
      highlightedSuggestion = -1;
    }

    function highlightSuggestion(direction) {
      if (!suggestions || suggestions.hidden) {
        return;
      }

      var options = toArray(suggestions.querySelectorAll("[data-glossary-suggestion]"));

      if (!options.length) {
        return;
      }

      highlightedSuggestion += direction;

      if (highlightedSuggestion < 0) {
        highlightedSuggestion = options.length - 1;
      } else if (highlightedSuggestion >= options.length) {
        highlightedSuggestion = 0;
      }

      options.forEach(function (option, index) {
        option.classList.toggle("is-highlighted", index === highlightedSuggestion);
      });

      options[highlightedSuggestion].focus();
    }

    function setQuery(value) {
      activeQuery = String(value || "").toLowerCase().trim();
      applyFilters();
      renderSuggestions();
    }

    if (searchInput) {
      searchInput.addEventListener(
        "input",
        debounce(function () {
          setQuery(searchInput.value);
        }, SEARCH_DEBOUNCE_MS)
      );

      searchInput.addEventListener("keydown", function (event) {
        if (event.key === "ArrowDown") {
          event.preventDefault();
          highlightSuggestion(1);
        } else if (event.key === "ArrowUp") {
          event.preventDefault();
          highlightSuggestion(-1);
        } else if (event.key === "Escape") {
          closeSuggestions();
          searchInput.blur();
        }
      });

      searchInput.addEventListener("focus", renderSuggestions);
    }

    if (suggestions) {
      suggestions.addEventListener("click", closeSuggestions);
    }

    chips.forEach(function (chip) {
      chip.addEventListener("click", function () {
        activeGroup = chip.getAttribute("data-glossary-group") || "all";

        chips.forEach(function (other) {
          var isActive = other === chip;
          other.classList.toggle("is-active", isActive);
          other.setAttribute("aria-pressed", isActive ? "true" : "false");
        });

        applyFilters();
      });
    });

    letterButtons.forEach(function (button) {
      button.addEventListener("click", function () {
        var letter = button.getAttribute("data-glossary-letter");
        var target = container.querySelector('[data-glossary-letter-section="' + letter + '"]');

        letterButtons.forEach(function (other) {
          other.classList.toggle("is-active", other === button);
        });

        if (target) {
          target.scrollIntoView({ behavior: "smooth", block: "start" });
        }
      });
    });

    document.addEventListener("click", function (event) {
      if (!event.target.closest || !event.target.closest(".glossary__search-shell")) {
        closeSuggestions();
      }
    });

    applyFilters();
  }

  function initAll() {
    toArray(document.querySelectorAll("[data-glossary]")).forEach(function (container) {
      if (container.dataset[READY_FLAG] === "1") {
        return;
      }

      container.dataset[READY_FLAG] = "1";
      watchStickyOffset(container);
      createGlossary(container);
    });
  }

  function bindGlobalShortcut() {
    document.addEventListener("keydown", function (event) {
      if ((event.metaKey || event.ctrlKey) && String(event.key).toLowerCase() === "k") {
        var input = document.querySelector("[data-glossary] [data-glossary-search]");

        if (input) {
          event.preventDefault();
          input.focus();
          input.select();
        }
      }
    });
  }

  function watchForNewBlocks() {
    if (typeof MutationObserver === "undefined") {
      return;
    }

    var observer = new MutationObserver(debounce(initAll, 100));
    observer.observe(document.body, { childList: true, subtree: true });
  }

  function init() {
    initAll();
    bindGlobalShortcut();
    watchForNewBlocks();
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", init);
  } else {
    init();
  }
})();

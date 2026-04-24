/*
 * Client-side syntax highlighting for the Code Tabs block.
 * Shiki is loaded lazily from a local bundle.
 */
(function () {
  const BLOCK_SELECTOR = "[data-code-tabs]";
  const SHIKI_URL = window.codeTabsConfig?.shikiUrl || "";
  const DEFAULT_THEME = "github-dark";
  const LANGUAGE_ALIASES = {
    js: "javascript",
    ts: "typescript",
    cs: "csharp",
    "c#": "csharp",
    ".net": "csharp",
    dotnet: "csharp",
    yml: "yaml",
    md: "markdown",
    plaintext: "text",
    plain: "text",
    txt: "text",
    shell: "bash",
    sh: "bash",
    zsh: "bash",
    console: "bash",
    py: "python",
  };

  let shikiModulePromise = null;
  let highlighterPromise = null;

  function normalizeLanguage(language) {
    const normalized = String(language || "text").trim().toLowerCase();
    return LANGUAGE_ALIASES[normalized] || normalized || "text";
  }

  function getBlockTheme(block) {
    return block.dataset.shikiTheme || DEFAULT_THEME;
  }

  function getShikiModule() {
    if (!shikiModulePromise) {
      if (!SHIKI_URL) {
        shikiModulePromise = Promise.reject(new Error("Missing local Shiki bundle URL."));
        return shikiModulePromise;
      }

      shikiModulePromise = import(SHIKI_URL);
    }

    return shikiModulePromise;
  }

  async function getHighlighter() {
    if (!highlighterPromise) {
      highlighterPromise = getShikiModule().then(({ createCodeTabsHighlighter }) =>
        createCodeTabsHighlighter({
          defaultTheme: DEFAULT_THEME,
        })
      );
    }

    return highlighterPromise;
  }

  async function ensureLanguage(highlighter, language) {
    const normalized = normalizeLanguage(language);

    try {
      const resolvedLanguage = await highlighter.ensureLanguage(normalized);

      if (normalized !== "text" && resolvedLanguage === "text") {
        console.warn("[code-tabs] Unsupported local Shiki language:", normalized);
      }

      return resolvedLanguage;
    } catch (error) {
      console.warn("[code-tabs] Failed to load Shiki language:", language, error);
      return "text";
    }
  }

  async function ensureTheme(highlighter, theme) {
    try {
      return await highlighter.ensureTheme(theme);
    } catch (error) {
      console.warn("[code-tabs] Failed to load Shiki theme:", theme, error);
      return DEFAULT_THEME;
    }
  }

  async function highlightPanel(block, panel) {
    if (!panel || panel.dataset.codeTabsRendered) {
      return;
    }

    const sourceField = panel.querySelector("[data-code-tabs-source]");
    const output = panel.querySelector("[data-code-tabs-output]");

    if (!sourceField || !output) {
      return;
    }

    const code = sourceField.value.replace(/\r\n/g, "\n");
    const theme = getBlockTheme(block);

    try {
      const highlighter = await getHighlighter();
      const language = await ensureLanguage(highlighter, panel.dataset.language);
      const resolvedTheme = await ensureTheme(highlighter, theme);
      const html = highlighter.codeToHtml(code, {
        lang: language,
        theme: resolvedTheme,
      });

      output.innerHTML = html;
      panel.dataset.codeTabsRendered = "1";
    } catch (error) {
      panel.dataset.codeTabsRendered = "fallback";
      console.warn("[code-tabs] Failed to render highlighted code:", error);
    }
  }

  function getPanelByButton(block, button) {
    if (!button) {
      return null;
    }

    const panelId = button.getAttribute("aria-controls");
    return panelId ? document.getElementById(panelId) : null;
  }

  function getActivePanel(block) {
    return block.querySelector(".code-tabs__panel.is-active");
  }

  function setCopyButtonState(block, label, isCopied = false) {
    const copyButton = block.querySelector("[data-code-tabs-copy]");
    const copyLabel = block.querySelector(".code-tabs__copy-label");

    if (copyButton) {
      copyButton.classList.toggle("is-copied", isCopied);
      copyButton.setAttribute("aria-label", label);
      copyButton.setAttribute("title", label);
    }

    if (copyLabel) {
      copyLabel.textContent = label;
    }
  }

  async function copyText(value) {
    if (navigator.clipboard && window.isSecureContext) {
      await navigator.clipboard.writeText(value);
      return;
    }

    const helper = document.createElement("textarea");
    helper.value = value;
    helper.setAttribute("readonly", "");
    helper.style.position = "absolute";
    helper.style.left = "-9999px";
    document.body.appendChild(helper);
    helper.select();
    document.execCommand("copy");
    document.body.removeChild(helper);
  }

  async function activateTab(block, button, options = {}) {
    const { moveFocus = false } = options;
    const buttons = Array.from(block.querySelectorAll("[data-code-tabs-trigger]"));
    const nextPanel = getPanelByButton(block, button);

    if (!button || !nextPanel) {
      return;
    }

    buttons.forEach((item) => {
      const isActive = item === button;
      const panel = getPanelByButton(block, item);

      item.classList.toggle("is-active", isActive);
      item.setAttribute("aria-selected", isActive ? "true" : "false");

      if (panel) {
        panel.classList.toggle("is-active", isActive);
        panel.hidden = !isActive;
      }
    });

    await highlightPanel(block, nextPanel);

    if (moveFocus) {
      button.focus();
    }
  }

  function bindCopyButton(block) {
    const copyButton = block.querySelector("[data-code-tabs-copy]");

    if (!copyButton || copyButton.dataset.codeTabsBound) {
      return;
    }

    copyButton.dataset.codeTabsBound = "1";
    setCopyButtonState(block, block.dataset.copyLabel || "Copy");

    copyButton.addEventListener("click", async () => {
      const activePanel = getActivePanel(block);
      const sourceField = activePanel?.querySelector("[data-code-tabs-source]");
      const copyLabel = block.dataset.copyLabel || "Copy";
      const copiedLabel = block.dataset.copiedLabel || "Copied";

      if (!sourceField) {
        return;
      }

      try {
        await copyText(sourceField.value);
        setCopyButtonState(block, copiedLabel, true);

        window.clearTimeout(copyButton._resetLabelTimer);
        copyButton._resetLabelTimer = window.setTimeout(() => {
          setCopyButtonState(block, copyLabel);
        }, 1800);
      } catch (error) {
        console.warn("[code-tabs] Failed to copy code:", error);
        setCopyButtonState(block, copyLabel);
      }
    });
  }

  function bindTabs(block) {
    const buttons = Array.from(block.querySelectorAll("[data-code-tabs-trigger]"));

    buttons.forEach((button, index) => {
      if (button.dataset.codeTabsBound) {
        return;
      }

      button.dataset.codeTabsBound = "1";

      button.addEventListener("click", () => {
        activateTab(block, button);
      });

      button.addEventListener("keydown", (event) => {
        const currentIndex = buttons.indexOf(button);
        let nextIndex = null;

        if (event.key === "ArrowRight") {
          nextIndex = (currentIndex + 1) % buttons.length;
        } else if (event.key === "ArrowLeft") {
          nextIndex = (currentIndex - 1 + buttons.length) % buttons.length;
        } else if (event.key === "Home") {
          nextIndex = 0;
        } else if (event.key === "End") {
          nextIndex = buttons.length - 1;
        }

        if (nextIndex === null) {
          return;
        }

        event.preventDefault();
        activateTab(block, buttons[nextIndex], { moveFocus: true });
      });
    });
  }

  function initBlock(block) {
    if (!block || block.dataset.codeTabsReady) {
      return;
    }

    block.dataset.codeTabsReady = "1";

    bindTabs(block);
    bindCopyButton(block);

    const activeButton =
      block.querySelector('[data-code-tabs-trigger][aria-selected="true"]') ||
      block.querySelector("[data-code-tabs-trigger]");

    if (activeButton) {
      activateTab(block, activeButton);
    }
  }

  function initAll(root = document) {
    const blocks =
      root.matches?.(BLOCK_SELECTOR) ? [root] : Array.from(root.querySelectorAll(BLOCK_SELECTOR));

    blocks.forEach(initBlock);
  }

  function observeNewBlocks() {
    if (!document.body || document.body.dataset.codeTabsObserverReady) {
      return;
    }

    document.body.dataset.codeTabsObserverReady = "1";

    const observer = new MutationObserver((mutations) => {
      mutations.forEach((mutation) => {
        mutation.addedNodes.forEach((node) => {
          if (node.nodeType === 1) {
            initAll(node);
          }
        });
      });
    });

    observer.observe(document.body, {
      childList: true,
      subtree: true,
    });
  }

  function boot() {
    initAll();
    observeNewBlocks();
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", boot);
  } else {
    boot();
  }

  if (window.acf?.addAction) {
    window.acf.addAction("render_block_preview/type=oboto/code-tabs", (element) => {
      initAll(element[0] || element);
    });
  }
})();

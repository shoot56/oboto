(function () {
  function renderForms() {
    if (!window.hbspt || !window.hbspt.forms) return;
    document.querySelectorAll('.enterprise-guide-hero__form[data-portal-id][data-form-id]').forEach(function (target) {
      if (target.dataset.rendered) return;
      target.dataset.rendered = 'true';
      window.hbspt.forms.create({
        portalId: target.dataset.portalId,
        formId: target.dataset.formId,
        target: '#' + target.id,
        cssRequired: '',
        css: '.hs-form{font-family:Poppins,Arial,sans-serif;color:#fff;background:transparent}.hs-form label{color:#d5e0f5;font-size:13px}.hs-form input,.hs-form select,.hs-form textarea{box-sizing:border-box;width:100%;padding:12px 14px;border:1px solid #344767;border-radius:8px;background:#090f1e;color:#fff;font-family:inherit}.hs-form .hs-button{width:100%;padding:15px 20px;border:0;border-radius:8px;background:#4f7df3;color:#fff;font-family:inherit;font-size:15px;font-weight:600;cursor:pointer}.hs-form .hs-richtext,.hs-form .hs-richtext p{color:#a9b5d0;font-size:12px;line-height:1.5}.hs-form .hs-error-msg{color:#ff9c9c}.hs-form .hs-form-field{margin-bottom:14px}'
      });
    });
  }
  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', renderForms);
  else renderForms();
})();

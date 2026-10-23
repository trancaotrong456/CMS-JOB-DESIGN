(function () {
  'use strict';

  if (!window.cmsEmailNotifications) return;

  var selectors = '.home-newsletter-form, .pds-contact-newsletter-form';
  document.querySelectorAll(selectors).forEach(function (form) {
    var email = form.querySelector('input[type="email"]');
    var button = form.querySelector('button');
    if (!email || !button) return;

    var honeypot = document.createElement('input');
    honeypot.type = 'text';
    honeypot.name = 'website_url';
    honeypot.tabIndex = -1;
    honeypot.autocomplete = 'off';
    honeypot.setAttribute('aria-hidden', 'true');
    honeypot.style.cssText = 'position:absolute;left:-10000px;width:1px;height:1px;overflow:hidden';
    form.appendChild(honeypot);

    var status = document.createElement('p');
    status.className = 'cms-email-notice';
    status.setAttribute('role', 'status');
    status.setAttribute('aria-live', 'polite');
    status.hidden = true;
    form.insertAdjacentElement('afterend', status);

    var busy = false;
    function submit(event) {
      if (event) event.preventDefault();
      if (busy || !form.reportValidity()) return;

      busy = true;
      button.disabled = true;
      status.textContent = '';
      status.hidden = true;
      var body = { email: email.value.trim(), honeypot: honeypot.value };

      window.fetch(window.cmsEmailNotifications.endpoint, {
        method: 'POST',
        credentials: 'same-origin',
        headers: {
          'Content-Type': 'application/json',
          'X-WP-Nonce': window.cmsEmailNotifications.nonce
        },
        body: JSON.stringify(body)
      }).then(function (response) {
        return response.json().then(function (payload) {
          if (!response.ok) throw new Error(payload.message || window.cmsEmailNotifications.messages.error);
          status.textContent = payload.message || window.cmsEmailNotifications.messages.pending;
          status.hidden = false;
          email.value = '';
        });
      }).catch(function (error) {
        status.textContent = error.message || window.cmsEmailNotifications.messages.error;
        status.hidden = false;
      }).finally(function () {
        busy = false;
        button.disabled = false;
      });
    }

    form.addEventListener('submit', submit);
    button.addEventListener('click', submit);
  });
})();

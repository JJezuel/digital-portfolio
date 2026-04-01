(function () {
  'use strict';

  const form   = document.getElementById('contact-form');
  const btn    = document.getElementById('btn-connect');
  const status = document.getElementById('form-status');

  const fields = {
    name:      { el: document.getElementById('name'),      err: document.getElementById('err-name') },
    email:     { el: document.getElementById('email'),     err: document.getElementById('err-email') },
    category:  { el: document.getElementById('category'),  err: document.getElementById('err-category') },
    challenge: { el: document.getElementById('challenge'), err: document.getElementById('err-challenge') }
  };

  const emailRe = /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/;

  function validate() {
    let valid = true;

    function check(key, test) {
      const { el, err } = fields[key];
      const ok = test(el.value.trim());
      el.classList.toggle('invalid', !ok);
      err.classList.toggle('visible', !ok);
      if (!ok) valid = false;
    }

    check('name',      v => v.length >= 2);
    check('email',     v => emailRe.test(v));
    check('category',  v => v !== '');
    check('challenge', v => v.length >= 10);

    return valid;
  }

  /* Clear error on input */
  Object.values(fields).forEach(({ el, err }) => {
    el.addEventListener('input', () => {
      el.classList.remove('invalid');
      err.classList.remove('visible');
      status.className = 'form-status';
    });
  });

  form.addEventListener('submit', async function (e) {
    e.preventDefault();
    status.className = 'form-status';

    if (!validate()) return;

    btn.disabled = true;
    btn.textContent = 'Sending…';

    const body = new FormData(form);

    try {
      const res  = await fetch('https://jezueldubero.com/process.php', { method: 'POST', body });
      const data = await res.json();

      if (data.success) {
        status.textContent = data.message || "Message sent — I'll be in touch soon.";
        status.className   = 'form-status success';
        form.reset();
      } else {
        status.textContent = data.message || 'Something went wrong. Please try again.';
        status.className   = 'form-status error';
      }
    } catch (_) {
      status.textContent = 'Could not reach the server. Please try again later.';
      status.className   = 'form-status error';
    } finally {
      btn.disabled    = false;
      btn.textContent = 'Connect';
    }
  });
})();
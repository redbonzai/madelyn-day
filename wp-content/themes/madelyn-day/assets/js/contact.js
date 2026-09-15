(() => {
  const form = document.querySelector('#madelyn-contact-form');
  if (!form) return;

  form.addEventListener('submit', (event) => {
    event.preventDefault();
    if (!form.reportValidity()) return;

    const data = new FormData(form);
    const recipient = form.dataset.recipient;
    const name = String(data.get('name') || '').trim();
    const company = String(data.get('company') || '').trim();
    const email = String(data.get('email') || '').trim();
    const topic = String(data.get('topic') || '').trim();
    const message = String(data.get('message') || '').trim();
    const status = document.querySelector('#contact-status');

    if (!recipient) {
      status.textContent = 'The contact email has not been configured yet. Please try again later.';
      return;
    }

    const details = [`From: ${name} <${email}>`];
    if (company) details.push(`Company: ${company}`);
    details.push('', message);

    const subject = `[Madelyn Day Website] ${topic}`;
    const mailto = `mailto:${encodeURIComponent(recipient)}?subject=${encodeURIComponent(subject)}&body=${encodeURIComponent(details.join('\n'))}`;
    status.textContent = 'Opening your email app. Review the prepared message and press Send there.';
    window.location.href = mailto;
  });
})();

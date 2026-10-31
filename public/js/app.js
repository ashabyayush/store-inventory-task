const $ = (s) => document.querySelector(s);
const money = (n) => Number(n).toFixed(2);
const esc = (s) => String(s ?? '').replace(/[&<>"']/g, (c) =>
  ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));

// Currency symbol + 2-decimal amount, from Configuration setting
const cur = (n) => (window.APP?.currency?.symbol ?? '') + money(n);

async function api(path, opts = {}) {
  const csrf = document.querySelector('meta[name="csrf-token"]')?.content;

  const res = await fetch('/api' + path, {
    headers: {
      'Accept': 'application/json',
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': csrf,
    },
    ...opts,
  });

  if (res.status === 401 || res.status === 419) {
    window.location.href = '/login';
    return new Promise(() => {});
  }

  const data = await res.json().catch(() => ({}));
  if (!res.ok) throw data;
  return data;
}

const errText = (e) => e.errors
  ? Object.values(e.errors).flat().join(' ')
  : (e.message || 'Something went wrong');

// type: 'ok' | 'err'
const alertBox = (type, msg) =>
  `<div class="alert alert-${type === 'ok' ? 'success' : 'danger'} mt-3 mb-0" role="alert">${esc(msg)}</div>`;

async function fillCategorySelect(select, withAll = false) {
  const cats = await api('/categories');
  select.innerHTML = (withAll ? '<option value="">All categories</option>' : '') +
    cats.map((c) => `<option value="${c.id}">${esc(c.name)}</option>`).join('');
  return cats;
}

/* ---------- popup dialogs (uses #appModal from the layout) ---------- */
function dialog({ title, message, okLabel = 'OK', okClass = 'btn-primary', cancel = false }) {
  return new Promise((resolve) => {
    const el = document.getElementById('appModal');
    const modal = bootstrap.Modal.getInstance(el) || new bootstrap.Modal(el);
    const ok = document.getElementById('appModalOk');

    document.getElementById('appModalTitle').textContent = title;
    document.getElementById('appModalBody').textContent = message;
    ok.textContent = okLabel;
    ok.className = 'btn ' + okClass;
    document.getElementById('appModalCancel').classList.toggle('d-none', !cancel);

    let result = false;
    const onOk = () => { result = true; modal.hide(); };
    const onHidden = () => {
      ok.removeEventListener('click', onOk);
      el.removeEventListener('hidden.bs.modal', onHidden);
      resolve(result);
    };

    ok.addEventListener('click', onOk);
    el.addEventListener('hidden.bs.modal', onHidden);
    modal.show();
  });
}

// Information popup (single OK button)
const showPopup = (title, message) => dialog({ title, message });

// Yes/No popup, resolves true when the user confirms
const confirmDialog = (title, message, okLabel = 'Delete') =>
  dialog({ title, message, okLabel, okClass: 'btn-danger', cancel: true });

// Back/forward cache-la irundhu vandha page-a refresh pannu
window.addEventListener('pageshow', (e) => {
  if (e.persisted) window.location.reload();
});
let PRODUCTS = [];

const X_ICON = '<svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" ' +
  'stroke-width="2.5" stroke-linecap="round" aria-hidden="true">' +
  '<line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>';

function lineHtml() {
  const opts = PRODUCTS.map((p) =>
    `<option value="${p.id}">${esc(p.name)} [${esc(p.category.name)}] · ${cur(p.price)} · stock ${p.stock}</option>`
  ).join('');

  return `<div class="line line-grid">
    <div class="f-product">
      <label class="form-label small text-muted mb-1">Product</label>
      <select class="form-select prod">${opts}</select>
    </div>
    <div class="f-qty">
      <label class="form-label small text-muted mb-1">Qty</label>
      <input class="form-control q" type="number" min="1" value="1">
    </div>
    <div class="f-remove">
      <label class="form-label small mb-1 invisible">Remove</label>
      <button class="btn btn-outline-danger btn-remove rm" type="button" title="Remove this line">
        ${X_ICON}<span>Remove</span>
      </button>
    </div>
  </div>`;
}

// Oru line mattum irundha Remove disable
function syncRemoveButtons() {
  const lines = document.querySelectorAll('.line');
  lines.forEach((l) => (l.querySelector('.rm').disabled = lines.length === 1));
}

function addLine() {
  $('#lines').insertAdjacentHTML('beforeend', lineHtml());
  const line = $('#lines').lastElementChild;
  line.querySelector('.rm').onclick = () => { line.remove(); syncRemoveButtons(); preview(); };
  line.querySelector('.prod').onchange = preview;
  line.querySelector('.q').oninput = preview;
  syncRemoveButtons();
  preview();
}

const collectItems = () => [...document.querySelectorAll('.line')].map((l) => ({
  product_id: +l.querySelector('.prod').value,
  quantity: +l.querySelector('.q').value,
}));

function preview() {
  let sub = 0, tax = 0;
  collectItems().forEach((i) => {
    const p = PRODUCTS.find((x) => x.id === i.product_id);
    if (!p) return;
    const s = p.price * i.quantity;
    sub += s;
    tax += (s * p.tax_percent) / 100;
  });
  $('#preview').innerHTML =
    `Subtotal ${cur(sub)} + tax ${cur(tax)} = <b class="fs-5 text-dark">${cur(sub + tax)}</b>`;
}

async function init() {
  PRODUCTS = await api('/products');
  $('#lines').innerHTML = '';
  addLine();
}

$('#addLine').onclick = addLine;

$('#placeOrder').onclick = async () => {
  const box = $('#orderMsg');
  box.innerHTML = '';
  try {
    const o = await api('/orders', {
      method: 'POST',
      body: JSON.stringify({
        customer_name: $('#cname').value,
        customer_email: $('#cemail').value,
        items: collectItems(),
      }),
    });
    box.innerHTML = alertBox('ok',
      `Order ${o.order_number} placed. Total ${cur(o.grand_total)}. Confirmation email queued.`);
    init();
  } catch (e) {
    box.innerHTML = alertBox('err', errText(e));
  }
};

init();
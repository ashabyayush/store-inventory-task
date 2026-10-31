let LIST = [];
let editModal;

async function loadProducts() {
  const cat = $('#filter').value;
  LIST = await api('/products' + (cat ? '?category_id=' + cat : ''));

  $('#prodTable').innerHTML = `<thead class="table-light"><tr>
      <th>Name</th><th>Code</th><th>Category</th>
      <th class="text-end">Price</th><th class="text-end">Tax %</th><th class="text-end">Stock</th><th></th>
    </tr></thead><tbody>` +
    (LIST.length ? LIST.map((p) => `<tr>
      <td>${esc(p.name)}</td><td><code>${esc(p.code)}</code></td>
      <td>${esc(p.category.name)}</td>
      <td class="text-end">${cur(p.price)}</td>
      <td class="text-end">${Number(p.tax_percent)}</td>
      <td class="text-end">${p.stock}</td>
      <td class="text-end text-nowrap">
        <button class="btn btn-sm btn-outline-primary" data-edit="${p.id}">
          <i class="bi bi-pencil"></i> <span class="d-none d-sm-inline">Edit</span>
        </button>
        <button class="btn btn-sm btn-outline-danger" data-del="${p.id}">
          <i class="bi bi-trash"></i> <span class="d-none d-sm-inline">Delete</span>
        </button>
      </td></tr>`).join('')
      : '<tr><td colspan="7" class="text-muted">No products found.</td></tr>') +
    '</tbody>';

  document.querySelectorAll('[data-edit]').forEach((b) =>
    (b.onclick = () => openEdit(+b.dataset.edit)));
  document.querySelectorAll('[data-del]').forEach((b) =>
    (b.onclick = () => deleteProduct(+b.dataset.del)));
}

function openEdit(id) {
  const p = LIST.find((x) => x.id === id);
  if (!p) return;
  $('#eid').value = p.id;
  $('#ename').value = p.name;
  $('#ecode').value = p.code;
  $('#ecat').value = p.category_id;
  $('#eprice').value = p.price;
  $('#etax').value = p.tax_percent;
  $('#estock').value = p.stock;
  $('#eMsg').innerHTML = '';
  editModal.show();
}

async function deleteProduct(id) {
  const p = LIST.find((x) => x.id === id);
  $('#pMsg').innerHTML = '';

  if (!(await confirmDialog('Delete product', `Delete "${p.name}" (${p.code})? This cannot be undone.`))) return;

  try {
    await api('/products/' + id, { method: 'DELETE' });
    $('#pMsg').innerHTML = alertBox('ok', 'Product deleted.');
    loadProducts();
  } catch (e) {
    await showPopup('Cannot delete product', errText(e));
  }
}

async function init() {
  editModal = new bootstrap.Modal($('#editModal'));
  await fillCategorySelect($('#pcat'));
  await fillCategorySelect($('#ecat'));
  await fillCategorySelect($('#filter'), true);
  loadProducts();
}

$('#filter').onchange = loadProducts;

$('#saveProduct').onclick = async () => {
  const box = $('#pMsg');
  box.innerHTML = '';
  try {
    await api('/products', {
      method: 'POST',
      body: JSON.stringify({
        name: $('#pname').value,
        code: $('#pcode').value,
        category_id: $('#pcat').value,
        price: $('#pprice').value,
        tax_percent: $('#ptax').value,
        stock: $('#pstock').value,
      }),
    });
    box.innerHTML = alertBox('ok', 'Product saved.');
    ['#pname', '#pcode', '#pprice'].forEach((s) => ($(s).value = ''));
    loadProducts();
  } catch (e) {
    box.innerHTML = alertBox('err', errText(e));
  }
};

$('#saveEdit').onclick = async () => {
  const box = $('#eMsg');
  box.innerHTML = '';
  try {
    await api('/products/' + $('#eid').value, {
      method: 'PUT',
      body: JSON.stringify({
        name: $('#ename').value,
        code: $('#ecode').value,
        category_id: $('#ecat').value,
        price: $('#eprice').value,
        tax_percent: $('#etax').value,
        stock: $('#estock').value,
      }),
    });
    editModal.hide();
    $('#pMsg').innerHTML = alertBox('ok', 'Product updated.');
    loadProducts();
  } catch (e) {
    box.innerHTML = alertBox('err', errText(e));
  }
};

init();
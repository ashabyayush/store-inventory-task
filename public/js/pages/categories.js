let CATS = [];
let editModal;

async function loadCategories() {
  CATS = await api('/categories');

  $('#catTable').innerHTML = `<thead class="table-light"><tr>
      <th>Name</th><th class="text-end">Products</th><th></th></tr></thead><tbody>` +
    (CATS.length ? CATS.map((c) => `<tr>
      <td>${esc(c.name)}</td>
      <td class="text-end">${c.products_count}</td>
      <td class="text-end text-nowrap">
        <button class="btn btn-sm btn-outline-primary" data-edit="${c.id}">
          <i class="bi bi-pencil"></i> <span class="d-none d-sm-inline">Edit</span>
        </button>
        <button class="btn btn-sm btn-outline-danger" data-del="${c.id}">
          <i class="bi bi-trash"></i> <span class="d-none d-sm-inline">Delete</span>
        </button>
      </td></tr>`).join('')
      : '<tr><td colspan="3" class="text-muted">No categories yet.</td></tr>') +
    '</tbody>';

  document.querySelectorAll('[data-edit]').forEach((b) => (b.onclick = () => {
    const c = CATS.find((x) => x.id === +b.dataset.edit);
    $('#eid').value = c.id;
    $('#ename').value = c.name;
    $('#eMsg').innerHTML = '';
    editModal.show();
  }));

  document.querySelectorAll('[data-del]').forEach((b) => (b.onclick = () => deleteCategory(+b.dataset.del)));
}

async function deleteCategory(id) {
  const c = CATS.find((x) => x.id === id);
  $('#cMsg').innerHTML = '';

  // Active products irundha popup, delete-ku poga vida maattom
  if (c.products_count > 0) {
    await showPopup(
      'Cannot delete category',
      `You have active products in this category (${c.products_count}). Move or delete them first.`
    );
    return;
  }

  if (!(await confirmDialog('Delete category', `Delete "${c.name}"?`))) return;

  try {
    await api('/categories/' + id, { method: 'DELETE' });
    $('#cMsg').innerHTML = alertBox('ok', 'Category deleted.');
    loadCategories();
  } catch (e) {
    await showPopup('Cannot delete category', errText(e));
    loadCategories();
  }
}

$('#addCat').onclick = async () => {
  $('#cMsg').innerHTML = '';
  try {
    await api('/categories', { method: 'POST', body: JSON.stringify({ name: $('#cname').value }) });
    $('#cname').value = '';
    loadCategories();
  } catch (e) {
    $('#cMsg').innerHTML = alertBox('err', errText(e));
  }
};

$('#saveEdit').onclick = async () => {
  $('#eMsg').innerHTML = '';
  try {
    await api('/categories/' + $('#eid').value, {
      method: 'PUT',
      body: JSON.stringify({ name: $('#ename').value }),
    });
    editModal.hide();
    $('#cMsg').innerHTML = alertBox('ok', 'Category updated.');
    loadCategories();
  } catch (e) {
    $('#eMsg').innerHTML = alertBox('err', errText(e));
  }
};

editModal = new bootstrap.Modal($('#editModal'));
loadCategories();
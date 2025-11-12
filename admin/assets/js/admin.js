// admin/assets/js/admin.js
// Small helper JS for admin UI
document.addEventListener('click', function (e) {
  // data-confirm attribute for delete links
  var el = e.target.closest && e.target.closest('[data-confirm]');
  if (!el) return;
  var msg = el.getAttribute('data-confirm') || 'Are you sure?';
  if (!confirm(msg)) e.preventDefault();
});

// Image preview with drag-drop sorting
window.imagePreviewHandler = (function() {
  var fileArray = [];
  var previewContainer = null;
  var fileInput = null;
  var sortOrderInputs = [];

  function init(inputId, containerId) {
    fileInput = document.getElementById(inputId);
    previewContainer = document.getElementById(containerId);
    if (!fileInput || !previewContainer) return;

    fileInput.addEventListener('change', handleFileSelect);
  }

  function handleFileSelect(e) {
    var files = Array.from(e.target.files);
    fileArray = files;
    renderPreviews();
  }

  function renderPreviews() {
    previewContainer.innerHTML = '';
    sortOrderInputs = [];

    fileArray.forEach(function(file, index) {
      if (!file.type.match('image.*')) return;

      var reader = new FileReader();
      reader.onload = function(ev) {
        var div = document.createElement('div');
        div.className = 'preview-item';
        div.draggable = true;
        div.dataset.index = index;

        var img = document.createElement('img');
        img.src = ev.target.result;

        var orderSpan = document.createElement('span');
        orderSpan.className = 'sort-order';
        orderSpan.textContent = index + 1;

        var removeBtn = document.createElement('button');
        removeBtn.className = 'remove-img';
        removeBtn.innerHTML = '×';
        removeBtn.type = 'button';
        removeBtn.onclick = function() { removeImage(index); };

        div.appendChild(img);
        div.appendChild(orderSpan);
        div.appendChild(removeBtn);
        previewContainer.appendChild(div);

        // Drag events
        div.addEventListener('dragstart', handleDragStart);
        div.addEventListener('dragover', handleDragOver);
        div.addEventListener('drop', handleDrop);
        div.addEventListener('dragend', handleDragEnd);
      };
      reader.readAsDataURL(file);
    });

    updateFileInput();
  }

  function removeImage(index) {
    fileArray.splice(index, 1);
    renderPreviews();
  }

  var dragSrcIndex = null;
  function handleDragStart(e) {
    dragSrcIndex = parseInt(this.dataset.index);
    this.classList.add('dragging');
    e.dataTransfer.effectAllowed = 'move';
  }
  function handleDragOver(e) {
    if (e.preventDefault) e.preventDefault();
    e.dataTransfer.dropEffect = 'move';
    return false;
  }
  function handleDrop(e) {
    if (e.stopPropagation) e.stopPropagation();
    var dropIndex = parseInt(this.dataset.index);
    if (dragSrcIndex !== dropIndex) {
      var tmp = fileArray[dragSrcIndex];
      fileArray.splice(dragSrcIndex, 1);
      fileArray.splice(dropIndex, 0, tmp);
      renderPreviews();
    }
    return false;
  }
  function handleDragEnd(e) {
    this.classList.remove('dragging');
  }

  function updateFileInput() {
    // Create new DataTransfer to update file input
    var dt = new DataTransfer();
    fileArray.forEach(function(f) { dt.items.add(f); });
    fileInput.files = dt.files;
  }

  return { init: init };
})();


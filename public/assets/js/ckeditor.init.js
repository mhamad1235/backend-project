function initializeCKEditor() {
  const activeTabEditor = document.querySelector('.ckeditor-classic');
  // remove table, image, and media buttons
  if (activeTabEditor) {
    ClassicEditor.create(activeTabEditor, {
      toolbar: {
        items: ['heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', '|', 'undo', 'redo'],
      },
    })
      .then(function (editor) {
        activeEditorInstance = editor; // Store the active CKEditor instance
      })
      .catch(function (error) {
        console.error(error);
      });
  }
}

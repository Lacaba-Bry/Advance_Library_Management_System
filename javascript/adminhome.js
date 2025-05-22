function openModal() {
  document.getElementById("addBookModal").classList.remove("hidden");
}

function closeModal() {
  document.getElementById("addBookModal").classList.add("hidden");
}

document.addEventListener('DOMContentLoaded', () => {
  const fileInput = document.getElementById('filePath');

  fileInput.addEventListener('change', function () {
    const file = this.files[0];
    if (file && file.type === 'text/plain') {
      const reader = new FileReader();

      reader.onload = function (e) {
        const text = e.target.result;
        autoFillForm(text);
      };

      reader.readAsText(file);
    }
  });

  function autoFillForm(text) {
    const fields = {

      'Title': 'Title',
      'Author': 'Author',
      'Publisher': 'Publisher',
      'ISBN': 'ISBN',
      'Genre': 'Genre',
      'Status': 'Status',
      'Story Snippet': 'Story_Snippet',
      'Description': 'Description',
      'Story': 'Story',
    };

    let lines = text.split(/\r?\n/);
    let currentField = null;
    let storyBuffer = [];

    lines.forEach(line => {
      if (!line.trim()) return;

      const match = line.match(/^([\w\s]+):\s*(.*)$/);
      if (match && fields[match[1]]) {
        if (currentField === 'Story') {
          document.querySelector(`[name="${fields['Story']}"]`).value = storyBuffer.join('\n');
          storyBuffer = [];
        }

        currentField = match[1];
        const formFieldName = fields[currentField];

        if (formFieldName && formFieldName !== 'Story') {
          document.querySelector(`[name="${formFieldName}"]`).value = match[2];
        }
      } else {
        if (currentField === 'Story') {
          storyBuffer.push(line);
        }
      }
    });

    if (storyBuffer.length > 0) {
      document.querySelector(`[name="${fields['Story']}"]`).value = storyBuffer.join('\n');
    }
  }
});

/**
 * Premium Photo Upload Preview
 * Handles drag-and-drop, live preview, and file validation.
 */
(function() {
    'use strict';

    document.addEventListener('DOMContentLoaded', function() {
        var dropZone = document.getElementById('photoDropZone');
        var fileInput = document.getElementById('profile_picture');
        var browseBtn = document.getElementById('photoBrowseBtn');
        var previewWrap = document.getElementById('photoPreviewWrap');
        var fileNameEl = document.getElementById('photoFileName');

        if (!dropZone || !fileInput) return;

        // Browse button click
        if (browseBtn) {
            browseBtn.addEventListener('click', function() {
                fileInput.click();
            });
        }

        // Click on drop zone also opens file picker
        dropZone.addEventListener('click', function(e) {
            if (e.target === browseBtn || browseBtn.contains(e.target)) return;
            fileInput.click();
        });

        // File input change
        fileInput.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                handleFile(this.files[0]);
            }
        });

        // Drag and drop
        ['dragenter', 'dragover'].forEach(function(evt) {
            dropZone.addEventListener(evt, function(e) {
                e.preventDefault();
                e.stopPropagation();
                dropZone.classList.add('dragover');
            });
        });

        ['dragleave', 'drop'].forEach(function(evt) {
            dropZone.addEventListener(evt, function(e) {
                e.preventDefault();
                e.stopPropagation();
                dropZone.classList.remove('dragover');
            });
        });

        dropZone.addEventListener('drop', function(e) {
            var files = e.dataTransfer.files;
            if (files && files[0]) {
                // Validate it's an image
                if (!files[0].type.startsWith('image/')) {
                    alert('Please select an image file (JPG, PNG, GIF).');
                    return;
                }
                // Set to file input
                fileInput.files = files;
                handleFile(files[0]);
            }
        });

        function handleFile(file) {
            // Validate size (2MB)
            if (file.size > 2 * 1024 * 1024) {
                alert('File is too large. Maximum size is 2MB.');
                fileInput.value = '';
                return;
            }

            // Show file name
            if (fileNameEl) {
                fileNameEl.textContent = file.name + ' (' + formatBytes(file.size) + ')';
                fileNameEl.style.display = 'block';
            }

            // Update title
            var titleEl = dropZone.querySelector('.ds-photo-info .title');
            if (titleEl) titleEl.textContent = 'Photo selected!';

            // Live preview
            var reader = new FileReader();
            reader.onload = function(e) {
                var img = document.createElement('img');
                img.src = e.target.result;
                img.className = 'ds-photo-preview';
                img.id = 'photoPreview';
                img.style.animation = 'fadeInUp .3s ease-out';
                previewWrap.innerHTML = '';
                previewWrap.appendChild(img);
            };
            reader.readAsDataURL(file);
        }

        function formatBytes(bytes) {
            if (bytes < 1024) return bytes + ' B';
            if (bytes < 1048576) return (bytes / 1024).toFixed(1) + ' KB';
            return (bytes / 1048576).toFixed(1) + ' MB';
        }
    });
})();

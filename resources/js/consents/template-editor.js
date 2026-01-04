import Quill from 'quill';
import 'quill/dist/quill.snow.css';

const initConsentTemplateEditor = () => {
    const wrapper = document.querySelector('[data-consent-template-editor]');
    if (!wrapper) return;

    const editorElement = wrapper.querySelector('[data-editor]');
    const hiddenInput = wrapper.querySelector('[data-editor-input]');

    if (!editorElement || !hiddenInput) {
        return;
    }

    const quill = new Quill(editorElement, {
        theme: 'snow',
        placeholder: 'Escribe el cuerpo del consentimiento...',
        modules: {
            toolbar: [
                [{ header: [1, 2, 3, false] }],
                ['bold', 'italic', 'underline'],
                [{ list: 'ordered' }, { list: 'bullet' }],
                ['link'],
                ['clean'],
            ],
        },
    });

    const syncHiddenInput = () => {
        hiddenInput.value = quill.root.innerHTML;
    };

    quill.on('text-change', syncHiddenInput);
    syncHiddenInput();

    const placeholderButtons = wrapper.querySelectorAll('[data-placeholder-insert]');

    placeholderButtons.forEach((button) => {
        button.addEventListener('click', () => {
            const placeholder = button.dataset.placeholderInsert;
            if (!placeholder) return;

            const selection = quill.getSelection(true);
            const index = selection ? selection.index : quill.getLength();
            quill.insertText(index, placeholder);
            quill.setSelection(index + placeholder.length, 0);
            quill.focus();
        });
    });

    const form = wrapper.closest('form');
    if (form) {
        form.addEventListener('submit', syncHiddenInput);
    }
};

document.addEventListener('DOMContentLoaded', initConsentTemplateEditor);

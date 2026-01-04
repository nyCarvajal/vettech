import Quill from 'quill';
import 'quill/dist/quill.snow.css';

const initConsentTemplateEditor = () => {
    const wrapper = document.querySelector('[data-consent-template-editor]');
    if (!wrapper) return;

    const editorElement = wrapper.querySelector('[data-editor]');
    const hiddenInput = wrapper.querySelector('[data-editor-input]');
    const placeholders = JSON.parse(wrapper.dataset.placeholders || '{}');

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

    const placeholderPattern = /\{\{\s*([a-zA-Z0-9_.]+)\s*\}\}/g;

    const getPlaceholderChipHtml = (key) => {
        const label = placeholders?.[key]?.label || key;
        return `<span class="placeholder-chip" contenteditable="false" data-placeholder-key="${key}" data-placeholder-label="${label}"><span class="placeholder-chip-label">${label}</span><span class="placeholder-chip-key">${key}</span></span>`;
    };

    const convertChipsToPlaceholders = (html) => {
        const temp = document.createElement('div');
        temp.innerHTML = html;

        temp.querySelectorAll('.placeholder-chip').forEach((chip) => {
            const key = chip.dataset.placeholderKey;
            const placeholder = key ? `{{${key}}}` : chip.textContent || '';
            chip.replaceWith(placeholder);
        });

        return temp.innerHTML;
    };

    const convertPlaceholdersToChips = (html) =>
        html.replace(placeholderPattern, (_, key) => getPlaceholderChipHtml(key));

    const syncHiddenInput = () => {
        const cleaned = convertChipsToPlaceholders(quill.root.innerHTML);
        hiddenInput.value = cleaned;
    };

    const initialChippedHtml = convertPlaceholdersToChips(quill.root.innerHTML || '');
    quill.clipboard.dangerouslyPasteHTML(initialChippedHtml);

    quill.on('text-change', syncHiddenInput);
    syncHiddenInput();

    const placeholderButtons = wrapper.querySelectorAll('[data-placeholder-key]');

    placeholderButtons.forEach((button) => {
        button.addEventListener('click', () => {
            const key = button.dataset.placeholderKey;
            if (!key) return;

            const chipHtml = getPlaceholderChipHtml(key);
            const selection = quill.getSelection(true);
            const index = selection ? selection.index : quill.getLength();

            quill.clipboard.dangerouslyPasteHTML(index, chipHtml);
            quill.setSelection(index + 1, 0);
            quill.focus();
            syncHiddenInput();
        });
    });

    const form = wrapper.closest('form');
    if (form) {
        form.addEventListener('submit', syncHiddenInput);
    }
};

document.addEventListener('DOMContentLoaded', initConsentTemplateEditor);

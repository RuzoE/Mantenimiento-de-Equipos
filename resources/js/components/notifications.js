/**
 * Oculta automáticamente las alertas de sesión (mensajes flash) después de unos segundos.
 * Se aplica a cualquier elemento con el atributo [data-flash].
 */
const AUTO_DISMISS_MS = 6000;

function initFlashMessages() {
    document.querySelectorAll('[data-flash]').forEach((el) => {
        setTimeout(() => {
            el.style.transition = 'opacity .4s ease';
            el.style.opacity = '0';
            setTimeout(() => el.remove(), 400);
        }, AUTO_DISMISS_MS);
    });
}

document.addEventListener('DOMContentLoaded', initFlashMessages);

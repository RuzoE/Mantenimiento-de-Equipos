import Alpine from 'alpinejs';

/**
 * Repetidor de "actividades realizadas" del formulario de mantenimiento.
 * Uso en Blade: x-data="actividadesMantenimiento(@js($iniciales))"
 */
Alpine.data('actividadesMantenimiento', (iniciales = []) => ({
    items: Array.isArray(iniciales) && iniciales.length ? [...iniciales] : [''],

    add() {
        this.items.push('');
    },

    remove(index) {
        this.items.splice(index, 1);
        if (this.items.length === 0) {
            this.items.push('');
        }
    },
}));

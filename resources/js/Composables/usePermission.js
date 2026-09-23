import { usePage } from '@inertiajs/vue3';

export function usePermission() {
    const hasPermission = (name) => {
        const permissions = usePage().props.auth.user?.permissions || [];
        return permissions.includes(name);
    };

    const hasRole = (name) => {
        const roles = usePage().props.auth.user?.roles || [];
        return roles.includes(name);
    };

    return { hasPermission, hasRole };
}

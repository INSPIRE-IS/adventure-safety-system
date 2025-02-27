import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head } from '@inertiajs/react';

export default function Dashboard({ auth }) {
    console.log('auth dashboard', auth);
    return (
        <AuthenticatedLayout
            user={auth.user}
            header={<h2 className="font-semibold text-xl text-gray-800 leading-tight">Dashboard</h2>}
        >
            <Head title="Dashboard" />

        <h1 className="text-3xl font-bold">{auth?.team?.name}</h1>
        <h2 className="text-xl font-semibold text-gray-600">Dashboard</h2>
        </AuthenticatedLayout>
    );
}

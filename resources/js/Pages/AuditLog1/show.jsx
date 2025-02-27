import React from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link } from '@inertiajs/react';

const AuditLogDetails = ({ auth, auditLog, ...props }) => {
    console.log('props', props);
    return (
        <AuthenticatedLayout user={auth.user}>
            <Head title="Audit Log Details" />
            <div className="container mx-auto py-6">
                {/* Page Header */}
                <header className="mb-6">
                    <h1 className="text-3xl font-bold">Audit Log Details</h1>
                </header>

                {/* Back Button */}
                <div className="mb-4">
                    <Link
                        href={route('audit-logs.index')}
                        className="text-indigo-600 hover:text-indigo-900"
                    >
                        &larr; Back to List
                    </Link>
                </div>

                {/* Audit Log Details Table */}
                <div className="overflow-hidden bg-white shadow-md rounded-lg">
                    <table className="min-w-full border-collapse border border-gray-200">
                        <tbody className="divide-y divide-gray-200">
                            <tr>
                                <th className="px-6 py-3 text-left text-sm font-medium text-gray-500">
                                    ID
                                </th>
                                <td className="px-6 py-3 text-sm text-gray-900">
                                    {auditLog?.id}
                                </td>
                            </tr>
                            <tr>
                                <th className="px-6 py-3 text-left text-sm font-medium text-gray-500">
                                    Description
                                </th>
                                <td className="px-6 py-3 text-sm text-gray-900">
                                    {auditLog?.description}
                                </td>
                            </tr>
                            <tr>
                                <th className="px-6 py-3 text-left text-sm font-medium text-gray-500">
                                    Subject ID
                                </th>
                                <td className="px-6 py-3 text-sm text-gray-900">
                                    {auditLog?.subject_id}
                                </td>
                            </tr>
                            <tr>
                                <th className="px-6 py-3 text-left text-sm font-medium text-gray-500">
                                    Subject Type
                                </th>
                                <td className="px-6 py-3 text-sm text-gray-900">
                                    {auditLog?.subject_type}
                                </td>
                            </tr>
                            <tr>
                                <th className="px-6 py-3 text-left text-sm font-medium text-gray-500">
                                    User ID
                                </th>
                                <td className="px-6 py-3 text-sm text-gray-900">
                                    {auditLog?.user_id}
                                </td>
                            </tr>
                            <tr>
                                <th className="px-6 py-3 text-left text-sm font-medium text-gray-500">
                                    Properties
                                </th>
                                <td className="px-6 py-3 text-sm text-gray-900">
                                    {Object.entries(auditLog.properties).map(([key, value]) => (
                                        <div key={key}>
                                            <strong>{key}:</strong> {value}
                                        </div>
                                    ))}
                                </td>
                            </tr>
                            <tr>
                                <th className="px-6 py-3 text-left text-sm font-medium text-gray-500">
                                    Host
                                </th>
                                <td className="px-6 py-3 text-sm text-gray-900">
                                    {auditLog.host}
                                </td>
                            </tr>
                            <tr>
                                <th className="px-6 py-3 text-left text-sm font-medium text-gray-500">
                                    Created At
                                </th>
                                <td className="px-6 py-3 text-sm text-gray-900">
                                    {auditLog?.created_at}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                {/* Back Button */}
                <div className="mt-4">
                    <Link
                        href={route('audit-logs.index')}
                        className="text-indigo-600 hover:text-indigo-900"
                    >
                        &larr; Back to List
                    </Link>
                </div>
            </div>
        </AuthenticatedLayout>
    );
};

export default AuditLogDetails;
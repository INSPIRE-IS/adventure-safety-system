import React, { useState } from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link } from '@inertiajs/react';
import Pagination from '@/Components/Pagination';
import AuditLogModal from './AuditLogModal';

const AuditLogs = ({ auth, auditLogs, can}) => {
    console.log(auditLogs);
    console.log(can);
    console.log(auth);
    const [isModalOpen, setIsModalOpen] = useState(false);
    const [modalMode, setModalMode] = useState("create");
    const [auditLog, setAuditLog] = useState(null);

    const handleOpenModal = (mode, audit_log = null) => {
        console.log('mode ', mode);
        console.log('audit_log ', audit_log);
        setModalMode(mode);
        setAuditLog(audit_log);
        setIsModalOpen(true);
    };

    const handleCloseModal = () => {
        setIsModalOpen(false);
        setAuditLog(null);
    };

    const handleDelete = (id) => {
        if (confirm("Are you sure you want to delete this audit_log?")) {
            post(route("audit_logs.destroy", id), {
                _method: "DELETE",
            });
        }
    };
    return (
        <AuthenticatedLayout user={auth.user}>
            <Head title="Audit Logs" />
            <div className="container mx-auto py-6">
                {/* Page Header */}
                <header className="mb-6">
                    <h1 className="text-3xl font-bold">Audit Logs</h1>
                    <p className="text-gray-600">List of all audit logs in the system</p>
                </header>

                {/* Table */}
                <div className="overflow-x-auto bg-white shadow-md rounded-lg">
                    <table className="min-w-full divide-y divide-gray-200">
                        {/* Table Header */}
                        <thead className="bg-gray-50">
                            <tr>
                                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    ID
                                </th>
                                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Description
                                </th>
                                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Subject Type
                                </th>

                                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Created At
                                </th>
                                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        {/* Table Body */}
                        <tbody className="bg-white divide-y divide-gray-200">
                            {auditLogs.data.map((log) => (
                                <tr key={log.id}>
                                    <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {log.id}
                                    </td>
                                    <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {log.description}
                                    </td>
                                    <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {log.subject_type}
                                    </td>
                                    <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {log.created_at}
                                    </td>
                                    <td className="px-6 py-4 flex flex-row items-center justify-center">
                                        {can.show_audit_log && (  
                                            <button 
                                                onClick={() => handleOpenModal("view", log)}
                                                className="w-[45px] h-[45px] bg-gray-100 rounded-full flex items-center justify-center hover:bg-gray-200 mr-2"
                                            >
                                                <img src="./images/edit-icon.svg" alt="User Icon" className="w-45 h-45" />
                                            </button>
                                        )}
                                        {can.delete_audit_log && (
                                            <button
                                                onClick={() => handleDelete(log.id)}
                                                className="w-[45px] h-[45px] rounded-full flex items-center justify-center hover:bg-red-200"
                                            >
                                                <img src="./images/remove.svg" alt="User Icon" className="w-45 h-45" />
                                            </button>
                                        )}
                                    </td>
                                </tr>
                            ))}
                        </tbody>
                    </table>
                </div>
            {/* Pagination Links */}
            <div className="mt-4">
                <Pagination links={auditLogs.links} />
            </div>            
            {/* Modal */}
            <AuditLogModal
                isOpen={isModalOpen}
                onClose={handleCloseModal}
                mode={modalMode}
                auditLog={auditLog}
            />
            </div>
        </AuthenticatedLayout>
    );
};

export default AuditLogs;

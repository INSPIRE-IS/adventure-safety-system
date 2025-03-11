import React, { useEffect, useState } from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link, router, useForm , usePage } from "@inertiajs/react";
import Pagination from '@/Components/Pagination';
import AuditLogModal from './AuditLogModal';
import SearchBar from '@/Components/SearchBar';
import AddButton from '@/Components/AddButton';

const AuditLogsIndex = ({auth, auditLogs, can, searchTerm, sortColumn, sortDirection }) => {
    //const { auth } = usePage().props;
    //const { destroy } = useForm();
    console.log('auth on auditlog ', auth);
    const [search, setSearch] = useState(searchTerm);
    const [isModalOpen, setIsModalOpen] = useState(false);
    const [modalMode, setModalMode] = useState("create");
    const [selectedLog, setSelectedLog] = useState(null);

    const handleOpenModal = (mode, log = null) => {
        console.log('mode ', mode);
        console.log('auditlogs ', log);
        setModalMode(mode);
        setSelectedLog(log);
        setIsModalOpen(true);
    };

    const handleCloseModal = () => {
        setIsModalOpen(false);
        setSelectedAuditLog(null);
    };

    const handleSearchChange = (e) => {
        console.log('value ', e.target.value);
        setSearch(e.target.value);
    };

    const handleSearchSubmit = (e) => {
        e.preventDefault();
        // Redirect to the route with search query and other parameters
        console.log('search ', search);
        window.location.href = route('audit-logs.index', {
            search,
            sort: sortColumn,
            direction: sortDirection
        });
    };

    const handleDelete = (auditlog) => {
        if (confirm("Are you sure you want to delete this auditlog?")) {
            //post(route("audit-logs.destroy", auditlog), { _method: "DELETE",});
            router.delete(route('audit-logs.destroy', auditlog), {preserveScroll: true});
        }
    };

    return (
        <AuthenticatedLayout user={auth.user} is_admin={auth?.is_admin}>
            <Head title="AuditLogs" />
            <div className="container mx-auto py-6">
            {/* Header */}
            <div className="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-6">
                <SearchBar search={search} handleSearchChange={(e) => handleSearchChange(e)} handleSearchSubmit={handleSearchSubmit} />
                {can.audit_log_create && (
                <AddButton title='Add New AuditLog' icon="./images/add-icon.svg" onClick={() => handleOpenModal("create")} />
                )}
            </div>
            <header className="mb-6 flex items-center justify-between w-[95%]">
                <div className="flex flex-col  items-right">               
                    <h1 className="view-header">AuditLogs</h1>                    
                </div>
            </header>

            {/* AuditLogs Table */}
            <div className="overflow-x-auto bg-white rounded-lg">
                <table className="min-w-full divide-y divide-gray-200">
                    <thead className="bg-gray-50">
                        <tr>                          
                            <th className="px-6 py-3 table-header uppercase tracking-wider">
                            <Link 
                                href={route('audit-logs.index', { 
                                    sort: 'name', 
                                    direction: sortColumn === 'name' && sortDirection === 'asc' ? 'desc' : 'asc' 
                                })} 
                                className="flex flex-row justify-between items-center"
                            >
                                Description
                                <img src="./images/left-arrow-icon.svg" alt="filter" className="w-[16px] h-[15px] rounded-full ml-4" />
                            </Link>
                            </th>
                                <th className="px-6 py-3 text-left table-header uppercase tracking-wider">
                                    Subject Type
                                </th>

                                <th className="px-6 py-3 text-left table-header uppercase tracking-wider">
                                    Created At
                                </th>
                            <th className="px-6 py-3 uppercase tracking-wider">
                                
                            </th>
                        </tr>
                    </thead>
                    <tbody className="bg-white divide-y divide-gray-200">
                        {auditLogs?.data.map((log) => (
                            <tr key={log.id} className='h-[78px]' style={{borderBottomWidth: '12px', borderBottomColor: "#F8F8F8",}}>
                                    <td className=" px-6 py-4 whitespace-nowrap text-start justify-center   ">
                                        <div className="w-full h-[49px] flex-col justify-center items-start gap-[9px] inline-flex border-r border-gray-300 ">
                                                <div className="w-full table-text1 ">{log.description}</div>                                        
                                        </div>
                                    </td>
                                    <td className=" px-6 py-4 whitespace-nowrap text-start justify-center   ">
                                        <div className="w-full h-[49px] flex-col justify-center items-start gap-[9px] inline-flex border-r border-gray-300 ">
                                                <div className="w-full table-text1 ">{log.subject_type}</div>                                        
                                        </div>
                                    </td>
                                    <td className=" px-6 py-4 whitespace-nowrap text-start justify-center   ">
                                        <div className="w-full h-[49px] flex-col justify-center items-start gap-[9px] inline-flex border-r border-gray-300 ">
                                                <div className="w-full table-text1 ">{log.created_at}</div>                                        
                                        </div>
                                    </td>

                                    <td className="px-6 py-4 flex flex-row items-center justify-end">
                                    {can.audit_log_show && (
                                        <button
                                            onClick={() => handleOpenModal("view",log)}
                                            className="w-[45px] h-[45px] bg-gray-100 rounded-full flex items-center justify-center hover:bg-gray-200 mr-3"
                                        >
                                            <img src="./images/view-icon.svg" alt="View" width={48} height={48} />
                                        </button>                                        
                                    )}
                                    {can.audit_log_edit && (
                                        <button
                                            onClick={() => handleOpenModal("edit",log)}
                                            className="w-[45px] h-[45px] bg-gray-100 rounded-full flex items-center justify-center hover:bg-gray-200 mr-3"
                                        >
                                            <img src="./images/edit-icon.svg" alt="Edit" width={45} height={45} />
                                        </button>
                                    )}
                                    {can.audit_log_delete && (
                                        <button
                                            onClick={() => handleDelete(log)}
                                            className="w-[45px] h-[45px] rounded-full flex items-center justify-center hover:bg-red-200"
                                        >
                                            <img src="./images/remove-icon.svg" alt="Remove" width={45} height={45} />
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
                <Pagination links={auditLogs?.links} />
            </div>            
            {/* Modal */}
            <AuditLogModal
                isOpen={isModalOpen}
                onClose={handleCloseModal}
                mode={modalMode}
                auditLog={selectedLog}
            />
            </div>

        </AuthenticatedLayout>
    );
};

export default AuditLogsIndex;

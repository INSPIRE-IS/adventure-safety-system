import React, { useEffect, useState } from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link, router, useForm , usePage } from "@inertiajs/react";
import Pagination from '@/Components/Pagination';
import RoleModal from './RoleModal';
import SearchBar from '@/Components/SearchBar';
import AddButton from '@/Components/AddButton';

const RolesIndex = ({auth, roles, permissions, can, searchTerm, sortColumn, sortDirection }) => {
    //const { auth } = usePage().props;
    //const { destroy } = useForm();
    console.log('auth on role ', auth);
    const [search, setSearch] = useState(searchTerm);
    const [isModalOpen, setIsModalOpen] = useState(false);
    const [modalMode, setModalMode] = useState("create");
    const [selectedRole, setSelectedRole] = useState(null);

    const handleOpenModal = (mode, role = null) => {
        console.log('mode ', mode);
        console.log('role ', role);
        setModalMode(mode);
        setSelectedRole(role);
        setIsModalOpen(true);
    };

    const handleCloseModal = () => {
        setIsModalOpen(false);
        setSelectedRole(null);
    };

    const handleSearchChange = (e) => {
        console.log('value ', e.target.value);
        setSearch(e.target.value);
    };

    const handleSearchSubmit = (e) => {
        e.preventDefault();
        // Redirect to the route with search query and other parameters
        console.log('search ', search);
        window.location.href = route('roles.index', {
            search,
            sort: sortColumn,
            direction: sortDirection
        });
    };

    const handleDelete = (role) => {
        if (confirm("Are you sure you want to delete this role?")) {
            //post(route("roles.destroy", role), { _method: "DELETE",});
            router.delete(route('roles.destroy', role), {preserveScroll: true});
        }
    };

    return (
        <AuthenticatedLayout user={auth.user}>
            <Head title="Roles" />
            <div className="container mx-auto py-6">
            {/* Header */}
            <div className="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-6">
                <SearchBar search={search} handleSearchChange={(e) => handleSearchChange(e)} handleSearchSubmit={handleSearchSubmit} />
                {can.role_create && (
                <AddButton title='Add New Role' icon="./images/add-icon.svg" onClick={() => handleOpenModal("create")} />
                )}
            </div>
            <header className="mb-6 flex items-center justify-between w-[95%]">
                <div className="flex flex-col  items-right">             
                    <h1 className="view-header">Role Management</h1>                    
                </div>
            </header>

            {/* Roles Table */}
            <div className="overflow-x-auto bg-white rounded-lg">
                <table className="min-w-full divide-y divide-gray-200">
                    <thead className="bg-gray-50">
                        <tr>
                            <th className="px-6 py-3 table-header uppercase tracking-wider">
                            <Link 
                                href={route('roles.index', { 
                                    sort: 'name', 
                                    direction: sortColumn === 'name' && sortDirection === 'asc' ? 'desc' : 'asc' 
                                })} 
                                className="flex flex-row justify-center items-center"
                            >
                                Role Name
                                <img src="./images/left-arrow-icon.svg" alt="filter" className="w-[16px] h-[15px] rounded-full ml-4" />
                            </Link>
                            </th>
                            <th className="px-6 py-3 text-left table-header uppercase tracking-wider">
                            <Link 
                                href={route('roles.index', { 
                                    sort: 'role', 
                                    direction: sortColumn === 'role' && sortDirection === 'asc' ? 'desc' : 'asc' 
                                })} 
                                className="flex flex-row items-center"
                            >
                                Access Role
                                <img 
                                    src="./images/left-arrow-icon.svg" 
                                    alt="filter" 
                                    className="w-[16px] h-[15px] rounded-full ml-4" 
                                />
                            </Link>

                            </th>
                            <th className="px-6 py-3 uppercase tracking-wider">
                                
                            </th>
                        </tr>
                    </thead>
                    <tbody className="bg-white divide-y divide-gray-200">
                        {roles?.data.map((role) => (
                            <tr key={role.id} className='h-[78px]' style={{borderBottomWidth: '12px', borderBottomColor: "#F8F8F8",}}>
                                <td className="px-6 py-4 whitespace-nowrap text-start justify-center  table-text1 ">
                                        {role.name}
                                </td>
                                <td className="px-6 py-4 max-h-[100px] overflow-y-auto">
                                    <div className="max-h-[100px] overflow-y-auto">
                                        {role?.permissions.map((role) => (
                                            <h4 key={role.id} className="table-text2">
                                                {role.name}
                                            </h4>
                                        ))}
                                    </div>
                                </td>
                                <td className="px-6 py-4 flex flex-row items-center justify-end">
                                    {can.role_show && (
                                        <button
                                            onClick={() => handleOpenModal("view",role)}
                                            className="text-indigo-600 hover:text-indigo-900"
                                        >
                                            <img src="./images/view-icon.svg" alt="View" width={45} height={45} />
                                        </button>                                        
                                    )}
                                    {can.role_edit && (
                                        <button
                                            onClick={() => handleOpenModal("edit",role)}
                                            className="w-[45px] h-[45px] bg-gray-100 rounded-full flex items-center justify-center hover:bg-gray-200 mr-3"
                                        >
                                            <img src="./images/edit-icon.svg" alt="Edit" className="w-45 h-45" />
                                        </button>
                                    )}
                                    {can.role_delete && (
                                        <button
                                            onClick={() => handleDelete(role)}
                                            className="w-[45px] h-[45px] rounded-full flex items-center justify-center hover:bg-red-200"
                                        >
                                            <img src="./images/remove-icon.svg" alt="Remove" className="w-45 h-45" />
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
                <Pagination links={roles?.links} />
            </div>            
            {/* Modal */}
            <RoleModal
                isOpen={isModalOpen}
                onClose={handleCloseModal}
                mode={modalMode}
                role={selectedRole}
                permissions={permissions}
            />
            </div>

        </AuthenticatedLayout>
    );
};

export default RolesIndex;

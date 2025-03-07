import React, { useEffect, useState } from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link, router, useForm , usePage } from "@inertiajs/react";
import Pagination from '@/Components/Pagination';
import UserModal from './UserModal';
import SearchBar from '@/Components/SearchBar';
import AddButton from '@/Components/AddButton';

const UsersIndex = ({auth, users, roles, teams, can, searchTerm, sortColumn, sortDirection }) => {
    //const { auth } = usePage().props;
    //const { destroy } = useForm();
    console.log('auth on user ', auth);
    const [search, setSearch] = useState(searchTerm);
    const [isModalOpen, setIsModalOpen] = useState(false);
    const [modalMode, setModalMode] = useState("create");
    const [selectedUser, setSelectedUser] = useState(null);

    const handleOpenModal = (mode, user = null) => {
        console.log('mode ', mode);
        console.log('user ', user);
        setModalMode(mode);
        setSelectedUser(user);
        setIsModalOpen(true);
    };

    const handleCloseModal = () => {
        setIsModalOpen(false);
        setSelectedUser(null);
    };

    const handleSearchChange = (e) => {
        console.log('value ', e.target.value);
        setSearch(e.target.value);
    };

    const handleSearchSubmit = (e) => {
        e.preventDefault();
        // Redirect to the route with search query and other parameters
        console.log('search ', search);
        window.location.href = route('users.index', {
            search,
            sort: sortColumn,
            direction: sortDirection
        });
    };

    const handleDelete = (user) => {
        if (confirm("Are you sure you want to delete this user?")) {
            //post(route("users.destroy", user), { _method: "DELETE",});
            router.delete(route('users.destroy', user), {preserveScroll: true});
        }
    };

    return (
        <AuthenticatedLayout user={auth.user} is_admin={auth?.is_admin}>
            <Head title="Users" />
            <div className="container mx-auto py-6">
            {/* Header */}
            <div className="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-6">
                <SearchBar search={search} handleSearchChange={(e) => handleSearchChange(e)} handleSearchSubmit={handleSearchSubmit} />
                {can.user_create && (
                <AddButton title='Add New User' icon="./images/add-user-icon.svg" onClick={() => handleOpenModal("create")} />
                )}
            </div>
            <header className="mb-6 flex items-center justify-between w-[95%]">
                <div className="flex flex-col  items-right">
                    <h3 className="view-sub-header">{auth?.team?.name ?? 'Site Name'}</h3>                
                    <h1 className="view-header">Staff Management</h1>                    
                </div>
            </header>

            {/* Users Table */}
            <div className="overflow-x-auto bg-white rounded-lg">
                <table className="min-w-full divide-y divide-gray-200">
                    <thead className="bg-gray-50">
                        <tr>
                            <th className="px-6 py-3 table-header uppercase tracking-wider">
                            <Link 
                                href={route('users.index', { 
                                    sort: 'name', 
                                    direction: sortColumn === 'name' && sortDirection === 'asc' ? 'desc' : 'asc' 
                                })} 
                                className="flex flex-row justify-center items-center"
                            >
                                User Name
                                <img src="./images/left-arrow-icon.svg" alt="filter" className="w-[16px] h-[15px] rounded-full ml-4" />
                            </Link>
                            </th>
                            <th className="px-6 py-3 text-left table-header uppercase tracking-wider">
                            <Link 
                                href={route('users.index', { 
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
                        {users?.data.map((user) => (
                            <tr key={user.id} className='h-[78px]' style={{borderBottomWidth: '12px', borderBottomColor: "#F8F8F8",}}>
                                <td className="flex px-6 py-4 whitespace-nowrap table-text1 ">
                                    <div className="w-full flex flex-row items-center justify-start border-r border-[#CECECE]">
                                        <img src="./images/user-icon.png" alt="User Icon" className="w-45 h-45 mr-3" />
                                        {user.name}
                                    </div>

                                </td>
                                <td className="px-6 py-4 whitespace-nowrap ">
                                    {user?.roles.map((role) => 
                                        (
                                        <h4 className="table-text2">{role.name}</h4>
                                        )
                                    )}
                                </td>
                                <td className="px-6 py-4 flex flex-row items-center justify-end">
                                    {can.user_show && (
                                        <button
                                            onClick={() => handleOpenModal("view",user)}
                                            className="w-[45px] h-[45px] bg-gray-100 rounded-full flex items-center justify-center hover:bg-gray-200 mr-3"
                                        >
                                            <img src="./images/view-icon.svg" alt="View" width={45} height={45} />
                                        </button>                                        
                                    )}
                                    {can.user_edit && (
                                        <button
                                            onClick={() => handleOpenModal("edit",user)}
                                            className="w-[45px] h-[45px] bg-gray-100 rounded-full flex items-center justify-center hover:bg-gray-200 mr-3"
                                        >
                                            <img src="./images/edit-icon.svg" alt="Edit" className="w-45 h-45" />
                                        </button>
                                    )}
                                    {can.user_delete && (
                                        <button
                                            onClick={() => handleDelete(user)}
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
                <Pagination links={users?.links} />
            </div>            
            {/* Modal */}
            <UserModal
                isOpen={isModalOpen}
                onClose={handleCloseModal}
                mode={modalMode}
                user={selectedUser}
                roles={roles}
                teams={teams}
            />
            </div>

        </AuthenticatedLayout>
    );
};

export default UsersIndex;

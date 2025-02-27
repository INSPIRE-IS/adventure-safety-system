import React, { useState } from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head } from '@inertiajs/react';
import SearchBar from '@/Components/SearchBar';
import AddButton from '@/Components/AddButton';


const StaffManagement = ({ auth,can, ...props }) => {
    console.log('auth', auth);
    console.log('can', can);
    console.log('props', props);
    //const [staffMembers, setStaffMembers] = React.useState(props?.users);
    const [isModalOpen, setIsModalOpen] = useState(false);
    const [modalMode, setModalMode] = useState("create");
    const [selectedPermission, setSelectedPermission] = useState(null);

    const handleOpenModal = (mode, permission = null) => {
        console.log('mode ', mode);
        console.log('permission ', permission);
        setModalMode(mode);
        setSelectedPermission(permission);
        setIsModalOpen(true);
    };

    const handleCloseModal = () => {
        setIsModalOpen(false);
        setSelectedPermission(null);
    };

    const handleDelete = (id) => {
        if (confirm("Are you sure you want to delete this permission?")) {
            post(route("permissions.destroy", id), {
                _method: "DELETE",
            });
        }
    };

    return (
        <AuthenticatedLayout user={auth.user} >
        <Head title="Staff Management" />
        <div className="container py-6 bg-[#F8F8F8] w-full h-full ">
            {/* Search and Add Section */}
            <div className="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-6">
                <SearchBar onSearch={(query) => console.log('Searching for:', query)} />
                <AddButton onClick={() => console.log('Add Staff Member')} />
            </div>
            {/* Header Section */}            
            <header className="mb-6">
                <h1 className="text-3xl font-bold">Site Name</h1>
                <h2 className="text-xl font-semibold text-gray-600">Staff Management</h2>
            </header>
            {/* Table Headers */}
            <div className="hidden lg:grid grid-cols-3 gap-4 text-gray-700 font-semibold mb-2">
                <div className="col-span-1">Name</div>
                <div className="col-span-1">Role</div>
                <div className="col-span-1"></div>
            </div>

            {/* Staff Cards */}
            <div className="flex flex-col gap-4">
                {props?.users?.map((staff) => (
                    <>
                        <div className="hidden md:grid grid-cols-3 gap-4 text-gray-700 font-semibold mb-2">
                            <div className="col-span-1">{staff?.name}</div>
                            {staff?.roles.map((role) => {
                                const permissions = role?.permissions;
                                return (
                                    < >
                                        <div key={role.id} className="bg-gray-200 text-gray-600 rounded-full px-2 py-1 text-xs col-span-1">{role.name}</div>
                                        {/* {permissions?.map((permission) => (
                                            <div key={permission.id} className="bg-gray-200 text-gray-600 rounded-full px-2 py-1 text-xs col-span-1">{permission.name}</div>
                                        ))} */}
                                    </>
                                );
                            })}
                            <div className="col-span-1">
                            {can.show_staff && (
                                <button
                                    onClick={() =>
                                        handleOpenModal(
                                            "view",
                                            staff
                                        )
                                    }
                                    className="text-indigo-600 hover:text-indigo-900"
                                >
                                    View
                                </button>                                        
                            )}
                            {can.edit_staff && (
                                <button
                                    onClick={() =>
                                        handleOpenModal(
                                            "edit",
                                            staff
                                        )
                                    }
                                    className="text-blue-600 hover:text-blue-900"
                                >
                                    Edit
                                </button>
                            )}
                            {can.delete_staff && (
                                <button
                                    onClick={() =>
                                        handleDelete(staff.id)
                                    }
                                    className="text-red-600 hover:text-red-900"
                                >
                                    Delete
                                </button>
                            )}
                            </div>
                        </div>
                
                
                        <div className="flex md:hidden flex-col lg:flex-row justify-between items-center bg-white shadow-md p-4 rounded-lg">
                            <div className="font-semibold text-lg col-span-1" ></div>
                            {staff?.roles.map((role) => (
                                <div key={role.id} className="bg-gray-200 text-gray-600 rounded-full px-2 py-1 text-xs col-span-1">{role.name}</div>
                            ))}
                        </div>
                    </>  
                ))}
            </div>
        </div>
        </AuthenticatedLayout>
    );
};

export default StaffManagement;

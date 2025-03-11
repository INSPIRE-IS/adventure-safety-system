import React, { useEffect, useState } from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link, router, useForm , usePage } from "@inertiajs/react";
import Pagination from '@/Components/Pagination';
import TypeBoolModal from './UserAlertModal';
import SearchBar from '@/Components/SearchBar';
import AddButton from '@/Components/AddButton';

const UserAlertIndex = ({auth, userAlerts, roles, teams, can, searchTerm, sortColumn, sortDirection }) => {
    //const { auth } = usePage().props;
    //const { destroy } = useForm();
    console.log('auth on typebool ', auth);
    console.log('auth on typebool ', userAlerts);
    const [search, setSearch] = useState(searchTerm);
    const [isModalOpen, setIsModalOpen] = useState(false);
    const [modalMode, setModalMode] = useState("create");
    const [selectedTypeBool, setSelectedTypeBool] = useState(null);

    const handleOpenModal = (mode, userAlert = null) => {
        console.log('mode ', mode);
        console.log('userAlert ', userAlert);
        setModalMode(mode);
        setSelectedTypeBool(userAlert);
        setIsModalOpen(true);
    };

    const handleCloseModal = () => {
        setIsModalOpen(false);
        setSelectedTypeBool(null);
    };

    const handleSearchChange = (e) => {
        console.log('value ', e.target.value);
        setSearch(e.target.value);
    };

    const handleSearchSubmit = (e) => {
        e.preventDefault();
        // Redirect to the route with search query and other parameters
        console.log('search ', search);
        window.location.href = route('user-alerts.index', {
            search,
            sort: sortColumn,
            direction: sortDirection
        });
    };

    const handleDelete = (typebool) => {
        if (confirm("Are you sure you want to delete this typebool?")) {
            //post(route("user-alerts.destroy", typebool), { _method: "DELETE",});
            router.delete(route('user-alerts.destroy', typebool), {preserveScroll: true});
        }
    };

    return (
        <AuthenticatedLayout user={auth.user} is_admin={auth?.is_admin}>
            <Head title="Certification Types" />
            <div className="container mx-auto py-6">
            {/* Header */}
            <div className="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-6">
                <SearchBar search={search} handleSearchChange={(e) => handleSearchChange(e)} handleSearchSubmit={handleSearchSubmit} />
                {can.type_bool_create && (
                <AddButton title='Add New Certification Type' icon="./images/add-icon.svg" onClick={() => handleOpenModal("create")} />
                )}
            </div>
            <header className="mb-6 flex items-center justify-between w-[95%]">
                <div className="flex flex-col  items-right">               
                    <h1 className="view-header">User Alerts</h1>                    
                </div>
            </header>

            {/* UserAlert Table */}
            <div className="overflow-x-auto bg-white rounded-lg">
                <table className="min-w-full divide-y divide-gray-200">
                    <thead className="bg-gray-50">
                        <tr>
                            <th className="px-6 py-3 table-header uppercase tracking-wider">
                            <Link 
                                href={route('user-alerts.index', { 
                                    sort: 'name', 
                                    direction: sortColumn === 'name' && sortDirection === 'asc' ? 'desc' : 'asc' 
                                })} 
                                className="flex flex-row justify-between items-center"
                            >
                                Alert
                                <img src="./images/left-arrow-icon.svg" alt="filter" className="w-[16px] h-[15px] rounded-full ml-4" />
                            </Link>
                            </th>
                            <th className="px-6 py-3 table-header uppercase tracking-wider">
                                <div className="flex flex-row justify-between items-center" >
                                    In App Link
                                </div>
                                    
                            </th>
                            <th className="px-6 py-3 table-header uppercase tracking-wider">
                                <div className="flex flex-row justify-between items-center" >
                                    user
                                </div>
                            
                            </th>
                            <th className="px-6 py-3 uppercase tracking-wider">
                                
                            </th>
                        </tr>
                    </thead>
                    <tbody className="bg-white divide-y divide-gray-200">
                        {userAlerts?.data.map((userAlert) => (
                            <tr key={userAlert.id} className='h-[78px]' style={{borderBottomWidth: '12px', borderBottomColor: "#F8F8F8",}}>
                                <td className=" px-6 py-4 whitespace-nowrap text-start justify-center   ">
                                    <div className="w-full h-[49px] flex-col justify-center items-start gap-[9px] inline-flex border-r border-gray-300 ">
                                            <div className="w-full table-text1 ">{userAlert.alert_text}</div>                                        
                                    </div>
                                </td>
                                <td className=" px-6 py-4 whitespace-nowrap text-start justify-center   ">
                                    <div className="w-full h-[49px] flex-col justify-center items-start gap-[9px] inline-flex border-r border-gray-300 ">
                                            <div className="w-full table-text1 ">{userAlert.alert_link}</div>                                        
                                    </div>
                                </td>
                                <td className=" px-6 py-4 whitespace-nowrap text-start justify-center   ">
                                    <div className="w-full h-[49px] flex-col justify-center items-start gap-[9px] inline-flex border-r border-gray-300 ">
                                            <div className="w-full table-text1 ">
                                            {userAlert?.users.map((user) => 
                                        (
                                        <h4 className="table-text2">{user.name}</h4>
                                        )
                                    )}
                                                
                                    </div>                                        
                                    </div>
                                </td>

                                <td className="px-6 py-4 flex flex-row items-center justify-end">
                                    {can.type_bool_show && (
                                        <button
                                            onClick={() => handleOpenModal("view",userAlert)}
                                            className="text-indigo-600 hover:text-indigo-900"
                                        >
                                            View
                                        </button>                                        
                                    )}
                                    {can.type_bool_edit && (
                                        <button
                                            onClick={() => handleOpenModal("edit",userAlert)}
                                            className="w-[45px] h-[45px] bg-gray-100 rounded-full flex items-center justify-center hover:bg-gray-200 mr-3"
                                        >
                                            <img src="./images/edit-icon.svg" alt="Edit" className="w-45 h-45" />
                                        </button>
                                    )}
                                    {can.type_bool_delete && (
                                        <button
                                            onClick={() => handleDelete(userAlert)}
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
                <Pagination links={userAlerts?.links} />
            </div>            
            {/* Modal */}
            <TypeBoolModal
                isOpen={isModalOpen}
                onClose={handleCloseModal}
                mode={modalMode}
                typebool={selectedTypeBool}
                roles={roles}
                teams={teams}
            />
            </div>

        </AuthenticatedLayout>
    );
};

export default UserAlertIndex;

import React, { useEffect, useState } from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link, router, useForm , usePage } from "@inertiajs/react";
import Pagination from '@/Components/Pagination';
import TypeDrillModal from './TypeDrillModal';
import SearchBar from '@/Components/SearchBar';
import AddButton from '@/Components/AddButton';

const TypeDrillsIndex = ({auth,typeDrills, can, searchTerm, sortColumn, sortDirection }) => {
    //const { auth } = usePage().props;
    //const { destroy } = useForm();
    console.log('auth on typedrill ', auth);
    const [search, setSearch] = useState(searchTerm);
    const [isModalOpen, setIsModalOpen] = useState(false);
    const [modalMode, setModalMode] = useState("create");
    const [selectedTypeDrill, setSelectedTypeDrill] = useState(null);

    const handleOpenModal = (mode, typeDrill = null) => {
        console.log('mode ', mode);
        console.log('typedrill ', typeDrill);
        setModalMode(mode);
        setSelectedTypeDrill(typeDrill);
        setIsModalOpen(true);
    };

    const handleCloseModal = () => {
        setIsModalOpen(false);
        setSelectedTypeDrill(null);
    };

    const handleSearchChange = (e) => {
        console.log('value ', e.target.value);
        setSearch(e.target.value);
    };

    const handleSearchSubmit = (e) => {
        e.preventDefault();
        // Redirect to the route with search query and other parameters
        console.log('search ', search);
        window.location.href = route('type-drills.index', {
            search,
            sort: sortColumn,
            direction: sortDirection
        });
    };

    const handleDelete = (typedrill) => {
        if (confirm("Are you sure you want to delete this typedrill?")) {
            //post(route("typedrills.destroy", typedrill), { _method: "DELETE",});
            router.delete(route('type-drills.destroy', typedrill), {preserveScroll: true});
        }
    };

    return (
        <AuthenticatedLayout user={auth.user} is_admin={auth?.is_admin}>
            <Head title="TypeDrills" />
            <div className="container mx-auto py-6">
            {/* Header */}
            <div className="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-6">
                <SearchBar search={search} handleSearchChange={(e) => handleSearchChange(e)} handleSearchSubmit={handleSearchSubmit} />
                {can.type_drill_create && (
                <AddButton title='Add New Type Drill' icon="./images/add-icon.svg" onClick={() => handleOpenModal("create")} />
                )}
            </div>
            <header className="mb-6 flex items-center justify-between w-[95%]">
                <div className="flex flex-col  items-right">               
                    <h1 className="view-header">Type Drills</h1>                    
                </div>
                {can.type_drill_create && (
                    <div className="flex gap-3">
                        <Link
                            href={route('type-drills.import')}
                            className="bg-[#F6C546] hover:bg-[#f8bf2d] px-4 py-2 rounded-xl button-text1"
                        >
                            Import / Export
                        </Link>
                    </div>
                )}
            </header>

            {/* TypeDrills Table */}
            <div className="overflow-x-auto bg-white rounded-lg">
                <table className="min-w-full divide-y divide-gray-200">
                    <thead className="bg-gray-50">
                        <tr>
                            <th className="px-6 py-3 table-header uppercase tracking-wider">
                            <Link 
                                href={route('type-drills.index', { 
                                    sort: 'name', 
                                    direction: sortColumn === 'name' && sortDirection === 'asc' ? 'desc' : 'asc' 
                                })} 
                                className="flex flex-row justify-center items-center"
                            >
                                Type Drill Name
                                <img src="./images/left-arrow-icon.svg" alt="filter" className="w-[16px] h-[15px] rounded-full ml-4" />
                            </Link>
                            </th>
                            <th className="px-6 py-3 table-header uppercase tracking-wider">
                                Due Days
                            </th>
                            <th className="px-6 py-3 uppercase tracking-wider">
                                
                            </th>
                        </tr>
                    </thead>
                    <tbody className="bg-white divide-y divide-gray-200">
                        {typeDrills?.data.map((typeDrill) => (
                            <tr key={typeDrill.id} className='h-[78px]' style={{borderBottomWidth: '12px', borderBottomColor: "#F8F8F8",}}>
                                <td className=" px-6 py-4 whitespace-nowrap items-center justify-center  table-text1 ">
                                    {typeDrill.name}
                                </td>
                                <td className="px-6 py-4 whitespace-nowrap items-center justify-center table-text1">
                                    {typeDrill.due_days ?? 14}
                                </td>
                                <td className="px-6 py-4 flex flex-row items-center justify-end">
                                    {can.type_drill_show && (
                                        <button
                                            onClick={() => handleOpenModal("view",typeDrill)}
                                            className="w-[45px] h-[45px] bg-gray-100 rounded-full flex items-center justify-center hover:bg-gray-200 mr-3"
                                        >
                                            <img src="./images/view-icon.svg" alt="View" width={48} height={48} />
                                        </button>                                        
                                    )}
                                    {can.type_drill_edit && (
                                        <button
                                            onClick={() => handleOpenModal("edit",typeDrill)}
                                            className="w-[45px] h-[45px] bg-gray-100 rounded-full flex items-center justify-center hover:bg-gray-200 mr-3"
                                        >
                                            <img src="./images/edit-icon.svg" alt="Edit" width={45} height={45} />
                                        </button>
                                    )}
                                    {can.type_drill_delete && (
                                        <button
                                            onClick={() => handleDelete(typeDrill)}
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
                <Pagination links={typeDrills?.links} />
            </div>            
            {/* Modal */}
            <TypeDrillModal
                isOpen={isModalOpen}
                onClose={handleCloseModal}
                mode={modalMode}
                typeDrill={selectedTypeDrill}
            />
            </div>

        </AuthenticatedLayout>
    );
};

export default TypeDrillsIndex;

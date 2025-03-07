import React, { useEffect, useState } from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link, router, useForm , usePage } from "@inertiajs/react";
import Pagination from '@/Components/Pagination';
import DrillDetailModal from './DrillDetailModal';
import SearchBar from '@/Components/SearchBar';
import AddButton from '@/Components/AddButton';

const DrillDetailIndex = ({auth,drilldetails, sites,drills, instructors, signature_files, competents, members, can, searchTerm, sortColumn, sortDirection }) => {
    //const { auth } = usePage().props;
    //const { destroy } = useForm();
    console.log('auth on drilldetail ', auth);
    const [search, setSearch] = useState(searchTerm);
    const [isModalOpen, setIsModalOpen] = useState(false);
    const [modalMode, setModalMode] = useState("create");
    const [selectedDrillDetail, setSelectedDrillDetail] = useState(null);

    const handleOpenModal = (mode, drilldetail = null) => {
        console.log('mode ', mode);
        console.log('drilldetail ', drilldetail);
        setModalMode(mode);
        setSelectedDrillDetail(drilldetail);
        setIsModalOpen(true);
    };

    const handleCloseModal = () => {
        setIsModalOpen(false);
        setSelectedDrillDetail(null);
    };

    const handleSearchChange = (e) => {
        console.log('value ', e.target.value);
        setSearch(e.target.value);
    };

    const handleSearchSubmit = (e) => {
        e.preventDefault();
        // Redirect to the route with search query and other parameters
        console.log('search ', search);
        window.location.href = route('drill-details.index', {
            search,
            sort: sortColumn,
            direction: sortDirection
        });
    };

    const handleDelete = (drilldetail) => {
        if (confirm("Are you sure you want to delete this drilldetail?")) {
            //post(route("drilldetails.destroy", drilldetail), { _method: "DELETE",});
            router.delete(route('drill-details.destroy', drilldetail), {preserveScroll: true});
        }
    };

    return (
        <AuthenticatedLayout user={auth.user} is_admin={auth?.is_admin}>
            <Head title="Drill Details" />
            <div className="container mx-auto py-6">
            {/* Header */}
            <div className="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-6">
                <SearchBar search={search} handleSearchChange={(e) => handleSearchChange(e)} handleSearchSubmit={handleSearchSubmit} />
                {can.drill_detail_create && (
                <AddButton title='Add New DrillDetail' icon="./images/add-icon.svg" onClick={() => handleOpenModal("create")} />
                )}
            </div>
            <header className="mb-6 flex items-center justify-between w-[95%]">
                <div className="flex flex-col  items-right">               
                    <h1 className="view-header">Drill Details</h1>                    
                </div>
            </header>

            {/* DrillDetails Table */}
            <div className="overflow-x-auto bg-white rounded-lg">
                <table className="min-w-full divide-y divide-gray-200">
                    <thead className="bg-gray-50">
                        <tr>
                            <th className="px-6 py-3 table-header uppercase tracking-wider">
                            <Link 
                                href={route('drill-details.index', { 
                                    sort: 'name', 
                                    direction: sortColumn === 'name' && sortDirection === 'asc' ? 'desc' : 'asc' 
                                })} 
                                className="flex flex-row justify-center items-center"
                            >
                                Drill Detail Name
                                <img src="./images/left-arrow-icon.svg" alt="filter" className="w-[16px] h-[15px] rounded-full ml-4" />
                            </Link>
                            </th>
                            <th className="px-6 py-3 uppercase tracking-wider">
                                
                            </th>
                        </tr>
                    </thead>
                    <tbody className="bg-white divide-y divide-gray-200">
                        {drilldetails?.data.map((drilldetail) => (
                            <tr key={drilldetail.id} className='h-[78px]' style={{borderBottomWidth: '12px', borderBottomColor: "#F8F8F8",}}>
                                <td className=" px-6 py-4 whitespace-nowrap items-center justify-center  table-text1 ">
                                    {drilldetail.name}
                                </td>
                                <td className="px-6 py-4 flex flex-row items-center justify-end">
                                    {can.drill_detail_show && (
                                        <button
                                            onClick={() => handleOpenModal("view",drilldetail)}
                                            className="w-[45px] h-[45px] bg-gray-100 rounded-full flex items-center justify-center hover:bg-gray-200 mr-3"
                                        >
                                            <img src="./images/view-icon.svg" alt="View" width={48} height={48} />
                                        </button>                                        
                                    )}
                                    {can.drill_detail_edit && (
                                        <button
                                            onClick={() => handleOpenModal("edit",drilldetail)}
                                            className="w-[45px] h-[45px] bg-gray-100 rounded-full flex items-center justify-center hover:bg-gray-200 mr-3"
                                        >
                                            <img src="./images/edit-icon.svg" alt="Edit" width={45} height={45} />
                                        </button>
                                    )}
                                    {can.drill_detail_delete && (
                                        <button
                                            onClick={() => handleDelete(drilldetail)}
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
                <Pagination links={drilldetails?.links} />
            </div>            
            {/* Modal */}
            <DrillDetailModal
                isOpen={isModalOpen}
                onClose={handleCloseModal}
                mode={modalMode}
                drilldetail={selectedDrillDetail}
                sites={sites}
                drills={drills}
                instructors={instructors}
                signature_files={signature_files}
                competents={competents}
                members={members}
            />
            </div>

        </AuthenticatedLayout>
    );
};

export default DrillDetailIndex;

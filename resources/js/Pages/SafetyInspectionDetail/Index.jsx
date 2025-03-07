import React, { useEffect, useState } from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link, router, useForm , usePage } from "@inertiajs/react";
import Pagination from '@/Components/Pagination';
import SafetyInspectionDetailModal from './SafetyInspectionDetailModal';
import SearchBar from '@/Components/SearchBar';
import AddButton from '@/Components/AddButton';

const SafetyInspectionDetailsIndex = ({auth, safetyInspectionDetails,sites,inspections,categoryItems,categoryDetails, can, searchTerm, sortColumn, sortDirection }) => {
    //const { auth } = usePage().props;
    //const { destroy } = useForm();
    console.log('auth on safetyinspectiondetail ', auth);
    console.log('safetyInspectionDetails ', safetyInspectionDetails);
    console.log('inspections ', inspections);

    const [search, setSearch] = useState(searchTerm);
    const [isModalOpen, setIsModalOpen] = useState(false);
    const [modalMode, setModalMode] = useState("create");
    const [selectedSafetyInspectionDetail, setSelectedSafetyInspectionDetail] = useState(null);

    const handleOpenModal = (mode, safetyInspectionDetail = null) => {
        console.log('mode ', mode);
        console.log('safetyInspectionDetail ', safetyInspectionDetail);
        setModalMode(mode);
        setSelectedSafetyInspectionDetail(safetyInspectionDetail);
        setIsModalOpen(true);
    };

    const handleCloseModal = () => {
        setIsModalOpen(false);
        setSelectedSafetyInspectionDetail(null);
    };

    const handleSearchChange = (e) => {
        console.log('value ', e.target.value);
        setSearch(e.target.value);
    };

    const handleSearchSubmit = (e) => {
        e.preventDefault();
        // Redirect to the route with search query and other parameters
        console.log('search ', search);
        window.location.href = route('safetyinspectiondetails.index', {
            search,
            sort: sortColumn,
            direction: sortDirection
        });
    };

    const handleDelete = (safetyInspectionDetail) => {
        if (confirm("Are you sure you want to delete this safetyInspectionDetail?")) {
            //post(route("safetyinspectiondetails.destroy", safetyinspectiondetail), { _method: "DELETE",});
            router.delete(route('safety-inspection-details.destroy', safetyInspectionDetail), {preserveScroll: true});
        }
    };

    return (
        <AuthenticatedLayout user={auth.user} is_admin={auth?.is_admin}>
            <Head title="SafetyInspectionDetails" />
            <div className="container mx-auto py-6">
            {/* Header */}
            <div className="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-6">
                <SearchBar search={search} handleSearchChange={(e) => handleSearchChange(e)} handleSearchSubmit={handleSearchSubmit} />
                {can.safety_inspection_detail_create && (
                <AddButton title='Add New Safety Inspection Detail' icon="./images/add-icon.svg" onClick={() => handleOpenModal("create")} />
                )}
            </div>
            <header className="mb-6 flex items-center justify-between w-[95%]">
                <div className="flex flex-col  items-right">               
                    <h1 className="view-header">Safety Inspection Details</h1>                    
                </div>
            </header>

            {/* SafetyInspectionDetails Table */}
            <div className="overflow-x-auto bg-white rounded-lg">
                <table className="min-w-full divide-y divide-gray-200">
                    <thead className="bg-gray-50">
                        <tr>
                            <th className="px-6 py-3 table-header uppercase tracking-wider">
                            <Link 
                                href={route('safety-inspection-details.index', {
                                    sort: 'name', 
                                    direction: sortColumn === 'name' && sortDirection === 'asc' ? 'desc' : 'asc' 
                                })} 
                                className="flex flex-row justify-center items-center"
                            >
                                Safety Inspection Detail Name
                                <img src="./images/left-arrow-icon.svg" alt="filter" className="w-[16px] h-[15px] rounded-full ml-4" />
                            </Link>
                            </th>
                            <th className="px-6 py-3 uppercase tracking-wider">
                                
                            </th>
                        </tr>
                    </thead>
                    <tbody className="bg-white divide-y divide-gray-200">
                        {safetyInspectionDetails?.data.map((safetyInspectionDetail) => (
                            <tr key={safetyInspectionDetail.id} className='h-[78px]' style={{borderBottomWidth: '12px', borderBottomColor: "#F8F8F8",}}>
                                <td className=" px-6 py-4 whitespace-nowrap items-center justify-center  table-text1 ">
                                    {safetyInspectionDetail.name}
                                </td>
                                <td className="px-6 py-4 flex flex-row items-center justify-end">
                                    {can.safety_inspection_detail_show && (
                                        <button
                                            onClick={() => handleOpenModal("view",safetyInspectionDetail)}
                                            className="w-[45px] h-[45px] bg-gray-100 rounded-full flex items-center justify-center hover:bg-gray-200 mr-3"
                                        >
                                            <img src="./images/view-icon.svg" alt="View" width={48} height={48} />
                                        </button>                                        
                                    )}
                                    {can.safety_inspection_detail_edit && (
                                        <button
                                            onClick={() => handleOpenModal("edit",safetyInspectionDetail)}
                                            className="w-[45px] h-[45px] bg-gray-100 rounded-full flex items-center justify-center hover:bg-gray-200 mr-3"
                                        >
                                            <img src="./images/edit-icon.svg" alt="Edit" width={45} height={45} />
                                        </button>
                                    )}
                                    {can.safety_inspection_detail_delete && (
                                        <button
                                            onClick={() => handleDelete(safetyInspectionDetail)}
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
                <Pagination links={safetyInspectionDetails?.links} />
            </div>            
            {/* Modal */}
            <SafetyInspectionDetailModal
                isOpen={isModalOpen}
                onClose={handleCloseModal}
                mode={modalMode}
                safetyInspectionDetail={selectedSafetyInspectionDetail}
                sites={sites}   
                inspections={inspections} 
                categoryItems={categoryItems}
                categoryDetails={categoryDetails}
            />
            </div>

        </AuthenticatedLayout>
    );
};

export default SafetyInspectionDetailsIndex;

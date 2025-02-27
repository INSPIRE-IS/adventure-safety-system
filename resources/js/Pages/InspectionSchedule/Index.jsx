import React, { useEffect, useState } from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link, router, useForm , usePage } from "@inertiajs/react";
import Pagination from '@/Components/Pagination';
import InspectionScheduleModal from './InspectionScheduleModal';
import SearchBar from '@/Components/SearchBar';
import AddButton from '@/Components/AddButton';

// const periods = [
//     { value: "1", label: "None" },
//     { value: "2", label: "3 Months" },
//     { value: "3", label: "6 Months" },
//     { value: "4", label: "Annually" },
//   ];
//   const actives = [
//     { value: "1", label: "No" },
//     { value: "2", label: "Yes" },
//   ];

const InspectionSchedulesIndex = ({auth, inspectionSchedules, can, searchTerm, sortColumn, sortDirection, sites,typeActivities,periods, typeInspections, categoryItems, users }) => {
    //const { auth } = usePage().props;
    //const { destroy } = useForm();
    console.log('auth on inspectionSchedules ', auth);
    console.log('inspectionSchedules ', inspectionSchedules);
    const [search, setSearch] = useState(searchTerm);
    const [isModalOpen, setIsModalOpen] = useState(false);
    const [modalMode, setModalMode] = useState("create");
    const [selectedInspectionSchedule, setSelectedInspectionSchedule] = useState(null);

    const handleOpenModal = (mode, inspectionSchedule = null) => {
        console.log('mode ', mode);
        console.log('inspectionSchedule ', inspectionSchedule);
        setModalMode(mode);
        setSelectedInspectionSchedule(inspectionSchedule);
        setIsModalOpen(true);
    };

    const handleCloseModal = () => {
        setIsModalOpen(false);
        setSelectedInspectionSchedule(null);
    };

    const handleSearchChange = (e) => {
        console.log('value ', e.target.value);
        setSearch(e.target.value);
    };

    const handleSearchSubmit = (e) => {
        e.preventDefault();
        // Redirect to the route with search query and other parameters
        console.log('search ', search);
        window.location.href = route('inspection-schedules.index', {
            search,
            sort: sortColumn,
            direction: sortDirection
        });
    };

    const handleDelete = (inspectionschedule) => {
        if (confirm("Are you sure you want to delete this inspectionschedule?")) {
            //post(route("inspectionschedules.destroy", inspectionschedule), { _method: "DELETE",});
            router.delete(route('inspection-schedules.destroy', inspectionschedule), {preserveScroll: true});
        }
    };

    return (
        <AuthenticatedLayout user={auth.user}>
            <Head title="Inspection Schedules" />
            <div className="container mx-auto py-6">
            {/* Header */}
            <div className="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-6">
                <SearchBar search={search} handleSearchChange={(e) => handleSearchChange(e)} handleSearchSubmit={handleSearchSubmit} />
                {can.inspection_schedule_create && (
                <AddButton title='Add New Inspection Schedule' icon="./images/add-icon.svg" onClick={() => handleOpenModal("create")} />
                )}
            </div>
            <header className="mb-6 flex items-center justify-between w-[95%]">
                <div className="flex flex-col  items-right">
                    <h3 className="view-sub-header">{auth?.team?.name ?? 'Site Name'}</h3>               
                    <h1 className="view-header">Inspection Schedules</h1>                    
                </div>
            </header>

            {/* InspectionSchedules Table */}
            <div className="overflow-x-auto bg-white rounded-lg">
                <table className="min-w-full divide-y divide-gray-200">
                    <thead className="bg-gray-50">
                        <tr>
                            <th className="px-6 py-3 table-header uppercase tracking-wider">
                            <Link 
                                href={route('inspection-schedules.index', { 
                                    sort: 'name', 
                                    direction: sortColumn === 'name' && sortDirection === 'asc' ? 'desc' : 'asc' 
                                })} 
                                className="flex flex-row justify-center items-center"
                            >
                                Activity
                                <img src="./images/left-arrow-icon.svg" alt="filter" className="w-[16px] h-[15px] rounded-full ml-4" />
                            </Link>
                            </th>
                            <th className="px-6 py-3 table-header uppercase tracking-wider text-start justify-center">
                                Inspection Type
                            </th>
                            <th className="px-6 py-3 table-header uppercase tracking-wider text-start justify-center">
                                Repeat Period
                            </th>
                            <th className="px-6 py-3 table-header uppercase tracking-wider text-start justify-center">
                                Schedule Run Date
                            </th>
                            <th className="px-6 py-3 uppercase tracking-wider">
                                
                            </th>
                        </tr>
                    </thead>
                    <tbody className="bg-white divide-y divide-gray-200">
                        {inspectionSchedules?.data.map((inspectionSchedule) => {
                            const periodName = periods.find(period => period.value === inspectionSchedule.period_id);
                            return(
                            <tr key={inspectionSchedule.id} className='h-[78px]' style={{borderBottomWidth: '12px', borderBottomColor: "#F8F8F8",}}>
                                <td className=" px-6 py-4 whitespace-nowrap items-center justify-center  table-text1 ">
                                    {inspectionSchedule.name}
                                </td>
                                <td className=" px-6 py-4 whitespace-nowrap items-center justify-center  table-text1 ">
                                    {inspectionSchedule.type_inspection.name}
                                </td>
                                <td className=" px-6 py-4 whitespace-nowrap items-center justify-center  table-text1 ">
                                    {periodName.label}
                                    
                                </td>
                                <td className=" px-6 py-4 whitespace-nowrap items-center justify-center  table-text1 ">
                                    {inspectionSchedule.active_date}
                                </td>                                
                                <td className="px-6 py-4 flex flex-row items-center justify-end">
                                    {can.inspection_schedule_show && (
                                        <button
                                            onClick={() => handleOpenModal("view",inspectionSchedule)}
                                            className="w-[45px] h-[45px] bg-gray-100 rounded-full flex items-center justify-center hover:bg-gray-200 mr-3"
                                        >
                                            <img src="./images/view-icon.svg" alt="View" width={48} height={48} />
                                        </button>                                        
                                    )}
                                    {can.inspection_schedule_edit && (
                                        <button
                                            onClick={() => handleOpenModal("edit",inspectionSchedule)}
                                            className="w-[45px] h-[45px] bg-gray-100 rounded-full flex items-center justify-center hover:bg-gray-200 mr-3"
                                        >
                                            <img src="./images/edit-icon.svg" alt="Edit" width={45} height={45} />
                                        </button>
                                    )}
                                    {can.inspection_schedule_delete && (
                                        <button
                                            onClick={() => handleDelete(inspectionSchedule)}
                                            className="w-[45px] h-[45px] rounded-full flex items-center justify-center hover:bg-red-200"
                                        >
                                            <img src="./images/remove-icon.svg" alt="Remove" width={45} height={45} />
                                        </button>
                                    )}
                                </td>
                            </tr>
                        )})}
                    </tbody>
                </table>
            </div>
            {/* Pagination Links */}
            <div className="mt-4">
                <Pagination links={inspectionSchedules?.links} />
            </div>            
            {/* Modal */}
            <InspectionScheduleModal
                auth={auth}
                isOpen={isModalOpen}
                onClose={handleCloseModal}
                mode={modalMode}
                inspectionSchedule={selectedInspectionSchedule}
                site={auth?.team?.name ?? 'Site Name'}
                typeActivities={typeActivities}
                typeInspections={typeInspections}
                categoryItems={categoryItems}
                periods={periods}
                users={users}
            />
            </div>

        </AuthenticatedLayout>
    );
};

export default InspectionSchedulesIndex;

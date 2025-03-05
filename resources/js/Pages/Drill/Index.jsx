import React, { useEffect, useState } from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link, router, useForm , usePage } from "@inertiajs/react";
import Pagination from '@/Components/Pagination';
import DrillModal from './DrillModal';
import SearchBar from '@/Components/SearchBar';
import AddButton from '@/Components/AddButton';
import EditButton from '@/Components/EditButton';
import CustomSelect2 from '@/Components/CustomSelect2';


const DrillsIndex = ({auth, drills, can, searchTerm, searchPeriod, searchMonth, searchShow, sortColumn, sortDirection, sites, typeDrills, users, periods, actives }) => {
    //const { auth } = usePage().props;
    //const { destroy } = useForm();
    console.log('auth on drill ', auth);
    console.log('drills ', drills);
    const [search, setSearch] = useState(searchTerm);
    const [period, setPeriod] = useState(searchPeriod);
    const [month, setMonth] = useState(searchMonth);
    const [show, setShow] = useState(searchShow);
    const [isModalOpen, setIsModalOpen] = useState(false);
    const [modalMode, setModalMode] = useState("create");
    const [selectedDrill, setSelectedDrill] = useState(null);

    const handleOpenModal = (mode, drill = null) => {
        console.log('mode ', mode);
        console.log('drills ', drill);
        setModalMode(mode);
        setSelectedDrill(drill);
        setIsModalOpen(true);
    };

    const handleCloseModal = () => {
        setIsModalOpen(false);
        setSelectedDrill(null);
    };

    const handleSearchChange = (e) => {
        console.log('value ', e.target.value);
        setSearch(e.target.value);
    };

    const handleSearchSubmit = (e) => {
        e.preventDefault();
        // Redirect to the route with search query and other parameters
        console.log('search ', search);
        window.location.href = route('drills.index', {
            search,
            sort: sortColumn,
            direction: sortDirection
        });
    };

    const handleDelete = (drill) => {
        if (confirm("Are you sure you want to delete this drill?")) {
            //post(route("drills.destroy", drill), { _method: "DELETE",});
            router.delete(route('drills.destroy', drill), {preserveScroll: true});
        }
    };

    return (
        <AuthenticatedLayout user={auth.user}>
            <Head title="Drills" />
            <div className="container mx-auto py-6">
            {/* Header */}
            <div className="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-6">
                <SearchBar search={search} handleSearchChange={(e) => handleSearchChange(e)} handleSearchSubmit={handleSearchSubmit} />
                {can.drill_create && (
                <AddButton title='Add New Drill' icon="./images/add-icon.svg" onClick={() => handleOpenModal("create")} />
                )}
            </div>
            <header className="mb-6 flex items-center justify-between w-[100%]">
                <div className="flex flex-col  items-right">               
                    <h1 className="view-header">{auth?.team?.name ?? 'Site Name'}</h1>                    
                </div>
                <div className="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-6">
                    {can.drill_edit_reminder && (
                    <EditButton title='Send Reminder' width="w-[214px]" height="h-[52px]" icon="./images/add-alert-clock-icon.svg" onClick={() => handleOpenModal("create")} />
                    )}
                    {can.drill_edit_inspector && (
                    <EditButton title='Assign New Inspector' width="w-[244px]" height="h-[52px]" icon="./images/add-user-icon-2.svg" onClick={() => handleOpenModal("create")} />
                    )}
                    {can.drill_edit_date && (
                    <EditButton title='Update Drill Date' width="w-[254px]" height="h-[52px]" icon="./images/clock-setting-gear-icon.svg" onClick={() => handleOpenModal("create")} />
                    )}
                </div>
            </header>

            {/* Drills Table */}
            <div className=" pl-[32px] rounded-3xl bg-white">

                <div className="overflow-x-auto">
                <table className="min-w-full divide-y divide-gray-200">
                <thead className="bg-white ">
                        <tr className="w-[100%]">
                            <th className="px-6 py-3 table-header2 uppercase tracking-wider w-[10%]">	

                            </th>
                            <th className="px-6 py-3 text-start table-header2 uppercase tracking-wider  w-[15%]">
                            User Name
                            </th>
                            {/* <th className="px-6 py-3 text-start table-header2 uppercase tracking-wider  w-[15%]">
                            Drill Name
                            </th> */}
                            <th className="px-6 py-3 table-header2 uppercase tracking-wider  w-[15%]">
                            Last Safety Drill
                            </th>
                            <th className="px-6 py-3 table-header2 uppercase tracking-wider  w-[15%]">
                            Next Drill
                            </th>
                            <th className="px-6 py-3 uppercase tracking-wider  w-[30%]">
                                
                            </th>
                        </tr>
                    </thead>
                    <tbody className="bg-white divide-y divide-gray-200">
                        {drills?.data.map((drill) => {
                            console.log("drill", drill);
                            let formattedDate = ''
                            if(drill?.complete_date != null){
                                let next_date = new Date(drill.complete_date);
                                next_date.setFullYear(next_date.getFullYear() + 1);
                                next_date = new Date();
                                formattedDate =next_date.toISOString().split("T")[0];
                            }



                        return(
                            <tr key={drill.id} className='h-[78px] w-full ' style={{borderBottomWidth: '12px', borderBottomColor: "#F8F8F8",}}>   
                                <td className=" px-6 py-4 whitespace-nowrap items-center justify-center  table-text1  ">
                                <div className='bg-[#8A94A6] w-[43px] h-[43px] rounded-full flex items-center justify-center hover:bg-gray-200'>
                                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M13.9739 11.3187L11.9919 6.68343C11.6309 5.83489 11.0927 5.35363 10.4784 5.32196C9.87052 5.2903 9.28161 5.71457 8.83201 6.52512L7.62885 8.68447C7.37556 9.1404 7.01461 9.41269 6.622 9.44436C6.22306 9.48235 5.82412 9.27338 5.50117 8.86178L5.36185 8.68447C4.91225 8.12089 4.355 7.84859 3.78509 7.90558C3.21517 7.96258 2.72757 8.35518 2.40462 8.99476L1.30912 11.1794C0.916508 11.971 0.954503 12.8892 1.41677 13.6364C1.87903 14.3836 2.68325 14.8332 3.56345 14.8332H11.6436C12.4921 14.8332 13.2837 14.409 13.7523 13.6997C14.2335 12.9905 14.3095 12.0976 13.9739 11.3187Z" fill="white"/>
                                        <path d="M4.44406 5.97455C5.62615 5.97455 6.58441 5.01628 6.58441 3.8342C6.58441 2.65212 5.62615 1.69385 4.44406 1.69385C3.26198 1.69385 2.30371 2.65212 2.30371 3.8342C2.30371 5.01628 3.26198 5.97455 4.44406 5.97455Z" fill="white"/>
                                    </svg>
                                </div>
                                </td>
                                <td className=" px-6 py-4 whitespace-nowrap text-start justify-center table-text1 ">
                                    
                                    <div className="w-[180px] h-[49px] flex-col justify-start items-start gap-[9px] inline-flex  border-r border-gray-300 ">
                                        <div className="w-full text-[#11142d] text-base font-semibold font-['Manrope']">{drill.assigned_to.name}</div>                             
                                    </div>
                                        {/* <div className="w-[50px] h-[0px] origin-top-left -rotate-90 border border-[#cecece]"></div>  */}
                                </td>

                                <td className=" px-6 py-4 whitespace-nowrap text-center justify-center   table-text1   ">

                                    <div className="w-[175px] h-[49px] flex-col justify-center items-start gap-[9px] inline-flex border-r border-gray-300 ">
                                            <div className="w-full text-[#11142d] text-base font-semibold font-['Manrope']">{drill?.complete_date?.split(" ")[0]}</div>                                        
                                    </div>
                                     
                                </td>
                                <td className=" px-6 py-4 whitespace-nowrap text-center justify-center table-text3 ">
                                <div className="w-full text-[#11142d] text-base font-semibold font-['Manrope']">{formattedDate}</div>
                                </td>
                                <td className="px-6 py-4 flex flex-row items-center justify-start ">
                                    {can.drill_show && (
                                        // <button
                                        //     onClick={() => handleOpenModal("view",drill)}
                                        //     className="w-[45px] h-[45px] bg-gray-100 rounded-full flex items-center justify-center hover:bg-gray-200 mr-3"
                                        // >
                                        //     <img src="./images/view-icon.svg" alt="View" width={48} height={48} />
                                        // </button>  
                                        <div className="w-[214px] h-[52px] px-6 py-[9px] rounded-xl border border-[#d0d0d0] justify-center items-center gap-[126px] inline-flex">
                                            <div className="text-[#bfbfbf] text-base font-semibold font-['Manrope'] leading-normal">View Report</div>
                                        </div>                                      
                                    )}
                                    {can.drill_edit && (
                                        <button
                                            onClick={() => handleOpenModal("edit",drill)}
                                            className="w-[45px] h-[45px] bg-gray-100 rounded-full flex items-center justify-center hover:bg-gray-200 mr-3"
                                        >
                                            <img src="./images/edit-icon.svg" alt="Edit" width={45} height={45} />
                                        </button>
                                        
                                    )}
                                    {can.drill_delete && (
                                        <button
                                            onClick={() => handleDelete(drill)}
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
            </div>
            {/* Pagination Links */}
            <div className="mt-4">
                <Pagination links={drills?.links} />
            </div>            
            {/* Modal */}
            <DrillModal
                auth={auth}
                isOpen={isModalOpen}
                onClose={handleCloseModal}
                mode={modalMode}
                drill={selectedDrill}
                sites={sites}
                typeDrills={typeDrills}
                periods={periods}
                actives={actives}
                users={users}
            />
            </div>

        </AuthenticatedLayout>
    );
};

export default DrillsIndex;

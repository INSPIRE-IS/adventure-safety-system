import React, { useEffect, useState } from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link, router, useForm , usePage } from "@inertiajs/react";
import Pagination from '@/Components/Pagination';
import InspectionModal from './InspectionModal';
import SearchBar from '@/Components/SearchBar';
import AddButton from '@/Components/AddButton';
import EditButton from '@/Components/EditButton';
import CustomSelect2 from '@/Components/CustomSelect2';
import ManageModal from '@/Components/ManageModal';
import ElementModal from './ElementModal';

const InspectionsIndex = ({auth, inspections, can, searchTerm,periods, searchPeriod,months, searchMonth, shows, searchShow, sortColumn, sortDirection, sites, typeInspections, categoryItems, users, typeBools }) => {
    //const { auth } = usePage().props;
    //const { destroy } = useForm();
    console.log('auth on inspection ', auth);
    console.log('inspections ', inspections);

    const [search, setSearch] = useState(searchTerm  || '');
    const [period, setPeriod] = useState(searchPeriod || '');
    const [month, setMonth] = useState(searchMonth  || '');
    const [show, setShow] = useState(searchShow  || '');
    const [isModalOpen, setIsModalOpen] = useState(false);
    const [isManageModalOpen, setIsManageModalOpen] = useState(false);
    const [modalMode, setModalMode] = useState("create");
    const [selectedInspection, setSelectedInspection] = useState(null);
    const [selectedElementItem, setSelectedElementItem] = useState(null);
    const [checkedState, setCheckedState] = useState([]);

    useEffect(() => {
        console.log('searchPeriod ', searchPeriod);
        setPeriod(searchPeriod);
        console.log('searchMonth ', searchMonth);
        setMonth(searchMonth);
        console.log('searchShow ', searchShow);
        setShow(searchShow);
    }, []);
    console.log('Props for CustomSelect2:', { period, periods });
    const handleOpenModal = (mode, inspection = null) => {
        console.log('mode ', mode);
        console.log('inspections ', inspection);
        setModalMode(mode);
        setSelectedInspection(inspection);
        setIsModalOpen(true);
    };

    const handleOpenManageModal = (mode) => {
        if(checkedState.length == 0){
            alert("Please select at least one inspection");
            return;
        }
        setModalMode(mode);
        setIsManageModalOpen(true);
    };

    const handleCloseModal = () => {
        setIsModalOpen(false);
        setSelectedInspection(null);
        setSelectedElementItem(null);
        setIsManageModalOpen(false);
    };

    const handleSearchChange = (e) => {
        console.log('value ', e.target.value);
        setSearch(e.target.value);
    };

    const handleSearchSubmit = (e) => {
        e.preventDefault();
        // Redirect to the route with search query and other parameters
        console.log('search ', search);
        window.location.href = route('inspections.index', {
            search,
            sort: sortColumn,
            direction: sortDirection
        });
    };

    const handleFilterSubmit = (e, filter) => {
        e.preventDefault();
        // Redirect to the route with search query and other parameters
        console.log('filter ', filter);
        if(filter == "period"){setPeriod(e.target.value);
        }
        if(filter == "month"){setMonth(e.target.value);
        }
        if(filter == "show"){setShow(e.target.value);}
        window.location.href = route('inspections.index', {
            filter,
            value: e.target.value
        });
    };

    const handleDelete = (inspection) => {
        if (confirm("Are you sure you want to delete this inspection?")) {
            //post(route("inspections.destroy", inspection), { _method: "DELETE",});
            router.delete(route('inspections.destroy', inspection), {preserveScroll: true});
        }
    };

        // Handle individual checkbox toggle
        const handleChecked = (inspection_id, category_id) => {
            const isChecked = checkedState.some(
                (item) => item.inspection_id === inspection_id && item.category_id === category_id
            );
            
            if (isChecked) {
                // Uncheck the item
                setCheckedState(checkedState.filter(
                    (item) => item.inspection_id !== inspection_id || item.category_id !== category_id
                ));
            } else {
                // Check the item
                setCheckedState([...checkedState, { inspection_id, category_id }]);
            }
        };
    
        // Handle "check all" toggle
        const handleCheckAll = () => {
            if (checkedState.length === inspections?.data.length) {
                // Uncheck all items
                setCheckedState([]);
            } else {
                // Check all items
                const allItems = inspections?.data.map((inspection) => ({
                    inspection_id: inspection.inspection_id,
                    category_id: inspection.category_id,
                }));
                setCheckedState(allItems);
            }
        };
    
        // Check if a specific inspection item is checked
        const isChecked = (inspection_id, category_id) => {
            return checkedState.some(
                (item) => item.inspection_id === inspection_id && item.category_id === category_id
            );
        };

    return (
        <AuthenticatedLayout user={auth.user} is_admin={auth?.is_admin}>
            <Head title="Inspections" />
            <div className="container mx-auto py-6">
            {/* Header */}
            <div className="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-6">
                <SearchBar search={search} handleSearchChange={(e) => handleSearchChange(e)} handleSearchSubmit={handleSearchSubmit} />
                {can.inspection_create && (
                <AddButton title='Add New Inspection' icon="./images/add-icon.svg" onClick={() => handleOpenModal("create")} />
                )}
            </div>
            <header className="mb-6 flex items-center justify-between w-[100%]">
                <div className="flex flex-col  items-right">               
                    <h1 className="view-header">{auth?.team?.name ?? 'Site Name'}</h1>                    
                </div>
                <div className="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-6">
                    {can.inspection_edit_reminder && (
                    <EditButton title='Send Reminder' width="w-[214px]" height="h-[52px]" icon="./images/add-alert-clock-icon.svg" onClick={() => handleOpenManageModal("reminder")} />
                    )}
                    {can.inspection_edit_inspector && (
                    <EditButton title='Assign New Inspector' width="w-[244px]" height="h-[52px]" icon="./images/add-user-icon-2.svg" onClick={() => handleOpenManageModal("assign")} />
                    )}
                    {can.inspection_edit_date && (
                    <EditButton title='Update Inspection Date' width="w-[254px]" height="h-[52px]" icon="./images/clock-setting-gear-icon.svg" onClick={() => handleOpenManageModal("change")} />
                    )}
                </div>
            </header>

            {/* Inspections Table */}
            <div className=" pl-[32px] rounded-3xl bg-white">
                <div className="pt-[49px]  pb-[32px] ">               
                    <h3 className="label-header-3">Inspections History</h3>                    
                </div>
                <div className="flex flex-col lg:flex-row lg:items-center lg:justify-start gap-4 mb-6">
                    <CustomSelect2
                        label="Period"
                        name="period"
                        value={period}
                        onChange={(e) => handleFilterSubmit(e,'period')}
                        mode="create"
                        options={periods}
                    />
                    <CustomSelect2
                        label="Month"
                        name="month"
                        value={month}
                        onChange={(e) => handleFilterSubmit(e,'month')}
                        mode="create"
                        options={months}
                    />
                    <CustomSelect2
                        label="Show"
                        name="show"
                        value={show}
                        onChange={(e) => handleFilterSubmit(e,'show')}
                        mode="create"
                        options={shows}
                    />
                </div>
                <div className="overflow-x-auto">
                <table className="min-w-full divide-y divide-gray-200">
                <thead className="bg-white ">
                        <tr className="w-[100%]">
                            <th className="px-6 py-3 table-header2 uppercase tracking-wider w-[10%]">	
                                {/* <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                    <rect x="1" y="1" width="18" height="18" rx="3" stroke="#E4E4E4" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"/>
                                </svg> */}
                                <button onClick={handleCheckAll}>
                                    {checkedState.length === inspections.length ? 'Uncheck All' : 'Check All'}
                                </button>
                            </th>
                            <th className="px-6 py-3 text-start table-header2 uppercase tracking-wider  w-[15%]">
                            Inspection
                            </th>
                            <th className="px-6 py-3 text-start table-header2 uppercase tracking-wider  w-[15%]">
                            Assigned to
                            </th>
                            <th className="px-6 py-3 text-start table-header2 uppercase tracking-wider  w-[15%]">
                            Status
                            </th>
                            <th className="px-6 py-3 text-start table-header2 uppercase tracking-wider  w-[15%]">
                            Date Processed
                            </th>
                            <th className="px-6 py-3 uppercase tracking-wider  w-[30%]">
                                
                            </th>
                        </tr>
                    </thead>
                    <tbody className="bg-white divide-y divide-gray-200">
                    {inspections?.data.map((inspection) => {
                            console.log("inspection", inspection);
                            let due_date = new Date(inspection.due_date);
                            let status = due_date > new Date() ? "Upcoming" : "Overdue";
                            if (inspection.complete_date != null) {
                                status = "Complete"; // inspection.complete_date > new Date() ? "complete" : "incomplete";
                            }


                        return(
                            <tr key={inspection.inspection_id + "-" + inspection.category_id} className='h-[78px] w-full ' style={{borderBottomWidth: '12px', borderBottomColor: "#F8F8F8",}}>   
                                <td className=" px-6 py-4 whitespace-nowrap items-center justify-center  table-text1  ">
                                <svg
                            onClick={() => handleChecked(inspection.inspection_id, inspection.category_id)}
                            xmlns="http://www.w3.org/2000/svg"
                            width="20"
                            height="20"
                            viewBox="0 0 20 20"
                            fill="none"
                        >
                            <rect
                                x="1"
                                y="1"
                                width="18"
                                height="18"
                                rx="3"
                                stroke="#E4E4E4"
                                strokeWidth="2"
                                strokeLinecap="round"
                                strokeLinejoin="round"
                            />
                            {isChecked(inspection.inspection_id, inspection.category_id) && (
                                <path
                                    d="M5 10l3 3l7-7"
                                    stroke="green"
                                    strokeWidth="2"
                                    strokeLinecap="round"
                                    strokeLinejoin="round"
                                />
                            )}
                        </svg>
                                
                                </td>
                                <td className=" px-6 py-4 whitespace-nowrap text-start justify-center   ">
                                    <div className="w-full h-[49px] flex-col justify-center items-start gap-[9px] inline-flex border-r border-gray-300 ">
                                    <div className="w-full table-text1 "># {inspection.id}</div>                                        
                                    {/* <div className="w-full table-text1 ">Description</div>*/}
                                    </div>
                                </td>
                                <td className=" px-6 py-4 whitespace-nowrap text-start justify-center   ">
                                    <div className="w-full h-[49px] flex-col justify-center items-start gap-[9px] inline-flex border-r border-gray-300 ">
                                            <div className="w-full table-text1 ">{inspection?.assigned_to?.name}</div>                                        
                                    </div>
                                </td>
                                <td className=" px-6 py-4 whitespace-nowrap text-start justify-center   ">
                                    <div className="w-full h-[49px] flex-col justify-center items-start gap-[9px] inline-flex border-r border-gray-300 ">
                                            <div className="w-full table-text1 ">
                                            <span
                                            className={`px-2 py-1 rounded-[4px] text-pill-box  ${
                                                status === 'Upcoming'
                                                    ? 'bg-[#eba535]'
                                                    : status === 'Overdue'
                                                    ? 'bg-[#F00]'
                                                    : 'bg-[#7FBA7A]'
                                            }`}
                                        >
                                            {status}
                                        </span>          
                                            </div>                                        
                                    </div>
                                </td>
                                <td className=" px-6 py-4 whitespace-nowrap text-start justify-center   ">
                                    <div className="w-full h-[49px] flex-col justify-center items-start gap-[9px] inline-flex border-r border-gray-300 ">
                                            <div className="w-full table-text1 ">{inspection.complete_date?.split(" ")[0]}</div>                                        
                                    </div>
                                </td>
                                <td className="px-6 py-4 flex flex-row items-center justify-end ">
                                    {can.inspection_show && (
                                        // <button
                                        //     onClick={() => handleOpenModal("view",inspection)}
                                        //     className="w-[45px] h-[45px] bg-gray-100 rounded-full flex items-center justify-center hover:bg-gray-200 mr-3"
                                        // >
                                        //     <img src="./images/view-icon.svg" alt="View" width={48} height={48} />
                                        // </button>  
                                        <button onClick={() => router.get(route('inspections.show', inspection.id))} className="w-[214px] h-[52px] px-6 py-[9px] rounded-xl border border-[#d0d0d0] justify-center items-center gap-[126px] inline-flex">
                                            <div className="text-[#bfbfbf] text-base font-semibold font-['Manrope'] leading-normal">View Job Card</div>
                                        </button>                                      
                                    )}
                                    {can.inspection_edit && (
                                        <button
                                            onClick={() => handleOpenModal("edit",inspection)}
                                            className="w-[45px] h-[45px] bg-gray-100 rounded-full flex items-center justify-center hover:bg-gray-200 mr-3"
                                        >
                                            <img src="./images/edit-icon.svg" alt="Edit" width={45} height={45} />
                                        </button>
                                        
                                    )}
                                    {can.inspection_delete && (
                                        <button
                                            onClick={() => handleDelete(inspection)}
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
                <Pagination links={inspections?.links} />
            </div>            
            {/* Modal */}
            {/* <InspectionModal
                isOpen={isModalOpen}
                onClose={handleCloseModal}
                mode={modalMode}
                inspection={selectedInspection}
                sites={sites}
                typeInspections={typeInspections}
                categoryItems={categoryItems}
                users={users}
            /> */}
            <ElementModal
                isOpen={isModalOpen}
                onClose={handleCloseModal}
                mode={modalMode}
                elementItem={setSelectedElementItem}
                type_inspections={typeInspections}
                teams={sites}
                type_bools={typeBools}
                team={auth?.team}
            />
            <ManageModal
                isOpen={isManageModalOpen}
                onClose={handleCloseModal}
                checkedState={checkedState}
                mode={modalMode}
                users={users}

            />
            </div>

        </AuthenticatedLayout>
    );
};

export default InspectionsIndex;

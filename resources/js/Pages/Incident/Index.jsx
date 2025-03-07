import React, { useEffect, useState } from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link, router, useForm , usePage } from "@inertiajs/react";
import Pagination from '@/Components/Pagination';
import IncidentModal from './IncidentModal';
import SearchBar from '@/Components/SearchBar';
import AddButton from '@/Components/AddButton';

const IncidentsIndex = ({auth, incidents, sites,typeIncidents, can, searchTerm, sortColumn, sortDirection }) => {
    //const { auth } = usePage().props;
    //const { destroy } = useForm();
    //console.log('auth on typeActivities ', typeActivities);
    const [search, setSearch] = useState(searchTerm);
    const [isModalOpen, setIsModalOpen] = useState(false);
    const [modalMode, setModalMode] = useState("create");
    const [selectedIncident, setSelectedIncident] = useState(null);

    const handleOpenModal = (mode, incident = null) => {
        console.log('mode ', mode);
        console.log('incident ', incident);
        setModalMode(mode);
        setSelectedIncident(incident);
        setIsModalOpen(true);
    };

    const handleCloseModal = () => {
        setIsModalOpen(false);
        setSelectedIncident(null);
    };

    const handleSearchChange = (e) => {
        console.log('value ', e.target.value);
        setSearch(e.target.value);
    };

    const handleSearchSubmit = (e) => {
        e.preventDefault();
        // Redirect to the route with search query and other parameters
        console.log('search ', search);
        window.location.href = route('incidents.index', {
            search,
            sort: sortColumn,
            direction: sortDirection
        });
    };

    const handleDelete = (incident) => {
        if (confirm("Are you sure you want to delete this incident?")) {
            //post(route("incidents.destroy", incident), { _method: "DELETE",});
            router.delete(route('incidents.destroy', incident), {preserveScroll: true});
        }
    };

    return (
        <AuthenticatedLayout user={auth.user} is_admin={auth?.is_admin}>
            <Head title="Incidents" />
            <div className="container mx-auto py-6">
            {/* Header */}
            <div className="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-6">
                <SearchBar search={search} handleSearchChange={(e) => handleSearchChange(e)} handleSearchSubmit={handleSearchSubmit} />
                {can.incident_create && (
                <AddButton title='Add New Incident' icon="./images/add-icon.svg" onClick={() => handleOpenModal("create")} />
                )}
            </div>
            <header className="mb-6 flex items-center justify-between w-[95%]">
                <div className="flex flex-col  items-right">               
                    <h1 className="view-header">{auth?.team?.name ?? 'Site Name'}</h1>                    
                </div>
            </header>

            {/* Incidents Table */}
            <div className="overflow-x-auto bg-white rounded-lg">
                <table className="min-w-full divide-y divide-gray-200">
                    <thead className="bg-gray-50">
                        <tr>
                        <th className="px-6 py-3 table-header uppercase tracking-wider">
                            </th>
                            <th className="px-6 py-3 table-header uppercase tracking-wider">
                            <Link 
                                href={route('incidents.index', { 
                                    sort: 'type_incident_id', 
                                    direction: sortColumn === 'type_incident_id' && sortDirection === 'asc' ? 'desc' : 'asc' 
                                })} 
                                className="flex flex-row justify-center items-center"
                            >
                                Incident Type
                                <img src="./images/left-arrow-icon.svg" alt="filter" className="w-[16px] h-[15px] rounded-full ml-4" />
                            </Link>                                


                            </th>
                            <th className="px-6 py-3 table-header uppercase tracking-wider">
                            <Link 
                                href={route('incidents.index', { 
                                    sort: 'incident_date', 
                                    direction: sortColumn === 'incident_date' && sortDirection === 'asc' ? 'desc' : 'asc' 
                                })} 
                                className="flex flex-row justify-center items-center"
                            >
                                Date
                                <img src="./images/left-arrow-icon.svg" alt="filter" className="w-[16px] h-[15px] rounded-full ml-4" />
                            </Link>                                
                            </th>

                            <th className="px-6 py-3 table-header uppercase tracking-wider">
                            <Link 
                                href={route('incidents.index', { 
                                    sort: 'incident_date', 
                                    direction: sortColumn === 'incident_date' && sortDirection === 'asc' ? 'desc' : 'asc' 
                                })} 
                                className="flex flex-row justify-center items-center"
                            >
                                Time of Incident
                                <img src="./images/left-arrow-icon.svg" alt="filter" className="w-[16px] h-[15px] rounded-full ml-4" />
                            </Link>
                            </th>
                            <th className="px-6 py-3 uppercase tracking-wider">
                                
                            </th>
                        </tr>
                    </thead>
                    <tbody className="bg-white divide-y divide-gray-200">
                        {incidents?.data.map((incident) => (
                            <tr key={incident.id} className='h-[78px]' style={{borderBottomWidth: '12px', borderBottomColor: "#F8F8F8",}}>
                                <td className=" px-6 py-4 whitespace-nowrap items-center justify-center  table-text1  ">
                                <div className='bg-[#8A94A6] w-[43px] h-[43px] rounded-full flex items-center justify-center hover:bg-gray-200'>
                                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M13.9739 11.3187L11.9919 6.68343C11.6309 5.83489 11.0927 5.35363 10.4784 5.32196C9.87052 5.2903 9.28161 5.71457 8.83201 6.52512L7.62885 8.68447C7.37556 9.1404 7.01461 9.41269 6.622 9.44436C6.22306 9.48235 5.82412 9.27338 5.50117 8.86178L5.36185 8.68447C4.91225 8.12089 4.355 7.84859 3.78509 7.90558C3.21517 7.96258 2.72757 8.35518 2.40462 8.99476L1.30912 11.1794C0.916508 11.971 0.954503 12.8892 1.41677 13.6364C1.87903 14.3836 2.68325 14.8332 3.56345 14.8332H11.6436C12.4921 14.8332 13.2837 14.409 13.7523 13.6997C14.2335 12.9905 14.3095 12.0976 13.9739 11.3187Z" fill="white"/>
                                        <path d="M4.44406 5.97455C5.62615 5.97455 6.58441 5.01628 6.58441 3.8342C6.58441 2.65212 5.62615 1.69385 4.44406 1.69385C3.26198 1.69385 2.30371 2.65212 2.30371 3.8342C2.30371 5.01628 3.26198 5.97455 4.44406 5.97455Z" fill="white"/>
                                    </svg>
                                </div>
                                </td>


                                <td className=" px-6 py-4 whitespace-nowrap text-center justify-center   table-text1   ">
                                <div className="w-[175px] h-[49px] flex-col justify-center items-start gap-[9px] inline-flex border-r border-gray-300 ">
                                        <div className="w-full text-[#11142d] text-base font-semibold font-['Manrope']">{incident.type_incident.name}</div>                                        
                                </div>
                                </td>
                                <td className=" px-6 py-4 whitespace-nowrap text-center justify-center   table-text1   ">
                                <div className="w-[175px] h-[49px] flex-col justify-center items-start gap-[9px] inline-flex border-r border-gray-300 ">
                                        <div className="w-full text-[#11142d] text-base font-semibold font-['Manrope']">{incident.incident_date?.split(" ")[0]}</div>                                        
                                </div>
                                </td>
                                <td className=" px-6 py-4 whitespace-nowrap text-center justify-center   table-text1   ">
                                <div className="w-[175px] h-[49px] flex-col justify-center items-start gap-[9px] inline-flex border-r border-gray-300 ">
                                        <div className="w-full text-[#11142d] text-base font-semibold font-['Manrope']">{incident.incident_date?.split(" ")[1]}</div>                                        
                                </div>                            
                                </td>
                                <td className="px-6 py-4 flex flex-row items-center justify-end">
                                    {can.incident_show && (
                                        // <button
                                        //     onClick={() => handleOpenModal("view",incident)}
                                        //     className="w-[45px] h-[45px] bg-gray-100 rounded-full flex items-center justify-center hover:bg-gray-200 mr-3"
                                        // >
                                        //     <img src="./images/view-icon.svg" alt="View" width={48} height={48} />
                                        // </button> 
                                        <button onClick={() => router.get(route('incidents.show', incident.id))}
                                            className="w-[214px] h-[52px] px-6 py-[9px] rounded-xl border border-[#d0d0d0] justify-center items-center gap-[126px] inline-flex hover:bg-gray-200">
                                            <div className="text-[#bfbfbf] text-base font-semibold font-['Manrope'] leading-normal ">View Report</div>
                                        </button>                                       
                                    )}
                                    {can.incident_edit && (
                                        <button
                                            onClick={() => handleOpenModal("edit",incident)}
                                            className="w-[45px] h-[45px] bg-gray-100 rounded-full flex items-center justify-center hover:bg-gray-200 mr-3"
                                        >
                                            <img src="./images/edit-icon.svg" alt="Edit" width={45} height={45} />
                                        </button>
                                    )}
                                    {can.incident_delete && (
                                        <button
                                            onClick={() => handleDelete(incident)}
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
                <Pagination links={incidents?.links} />
            </div>            
            {/* Modal */}
            <IncidentModal
                isOpen={isModalOpen}
                onClose={handleCloseModal}
                mode={modalMode}
                incident={selectedIncident}
                sites={sites}
                typeIncidents={typeIncidents}
            />
            </div>

        </AuthenticatedLayout>
    );
};

export default IncidentsIndex;

import React, { useEffect, useState } from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link, router, useForm , usePage } from "@inertiajs/react";
import Pagination from '@/Components/Pagination';
import PostIncidentModal from './PostIncidentModal';
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

const PostIncidentsIndex = ({auth, postIncidents,sites,users,post_incident_schedules, can, searchTerm, sortColumn, sortDirection }) => {
    //const { auth } = usePage().props;
    //const { destroy } = useForm();
    console.log('auth on postincidents ', auth);
    console.log('postIncidents ', postIncidents);
    const [search, setSearch] = useState(searchTerm);
    const [isModalOpen, setIsModalOpen] = useState(false);
    const [modalMode, setModalMode] = useState("create");
    const [selectedPostIncident, setSelectedPostIncident] = useState(null);

    const handleOpenModal = (mode, postincident = null) => {
        console.log('mode ', mode);
        console.log('postincident ', postincident);
        setModalMode(mode);
        setSelectedPostIncident(postincident);
        setIsModalOpen(true);
    };

    const handleCloseModal = () => {
        setIsModalOpen(false);
        setSelectedPostIncident(null);
    };

    const handleSearchChange = (e) => {
        console.log('value ', e.target.value);
        setSearch(e.target.value);
    };

    const handleSearchSubmit = (e) => {
        e.preventDefault();
        // Redirect to the route with search query and other parameters
        console.log('search ', search);
        window.location.href = route('post-incidents.index', {
            search,
            sort: sortColumn,
            direction: sortDirection
        });
    };

    const handleDelete = (postIncident) => {
        if (confirm("Are you sure you want to delete this postincident?")) {
            //post(route("post-incidents.destroy", postIncident), { _method: "DELETE",});
            router.delete(route('post-incident.destroy', postIncident), {preserveScroll: true});
        }
    };

    return (
        <AuthenticatedLayout user={auth.user}>
            <Head title="Post Incidents" />
            <div className="container mx-auto py-6">
            {/* Header */}
            <div className="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-6">
                <SearchBar search={search} handleSearchChange={(e) => handleSearchChange(e)} handleSearchSubmit={handleSearchSubmit} />
                {can.post_incident_create && (
                <AddButton title='Add New Post Incident' icon="./images/add-icon.svg" onClick={() => handleOpenModal("create")} />
                )}
            </div>
            <header className="mb-6 flex items-center justify-between w-[95%]">
                <div className="flex flex-col  items-right">
                    <h3 className="view-sub-header">{auth?.team?.name ?? 'Site Name'}</h3>               
                    <h1 className="view-header">Post Incidents</h1>                    
                </div>
            </header>

            {/* PostIncidents Table */}
            <div className="overflow-x-auto bg-white rounded-lg">
                <table className="min-w-full divide-y divide-gray-200">
                    <thead className="bg-gray-50">
                        <tr>
                            <th className="px-6 py-3 table-header uppercase tracking-wider">
                            <Link 
                                href={route('post-incidents.index', { 
                                    sort: 'due_date', 
                                    direction: sortColumn === 'due_date' && sortDirection === 'asc' ? 'desc' : 'asc' 
                                })} 
                                className="flex flex-row justify-center items-center"
                            >
                                Due Date
                                <img src="./images/left-arrow-icon.svg" alt="filter" className="w-[16px] h-[15px] rounded-full ml-4" />
                            </Link>
                            </th>
                            <th className="px-6 py-3 table-header uppercase tracking-wider text-start justify-center">
                            Complet Date
                            </th>
                            <th className="px-6 py-3 table-header uppercase tracking-wider text-start justify-center">
                            Incident
                            </th>
                            <th className="px-6 py-3 table-header uppercase tracking-wider text-start justify-center">
                            assigned_to_id
                            </th>
                            <th className="px-6 py-3 uppercase tracking-wider">
                                
                            </th>
                        </tr>
                    </thead>
                    <tbody className="bg-white divide-y divide-gray-200">
                        {postIncidents?.data.map((postIncident) => {
                            return(
                            <tr key={postIncident.id} className='h-[78px]' style={{borderBottomWidth: '12px', borderBottomColor: "#F8F8F8",}}>
                                <td className=" px-6 py-4 whitespace-nowrap items-center justify-center  table-text1 ">
                                    {postIncident.due_date}
                                </td>
                                <td className=" px-6 py-4 whitespace-nowrap items-center justify-center  table-text1 ">
                                    {postIncident.complete_date}
                                </td>
                                <td className=" px-6 py-4 whitespace-nowrap items-center justify-center  table-text1 ">
                                    {postIncident.post_incident_schedule.name}
                                </td>  
                                <td className=" px-6 py-4 whitespace-nowrap items-center justify-center  table-text1 ">
                                    {postIncident.assigned_to.name}
                                </td>                                
                                <td className="px-6 py-4 flex flex-row items-center justify-end">
                                    {can.post_incident_show && (
                                        <button
                                            onClick={() => handleOpenModal("view",postIncident)}
                                            className="w-[45px] h-[45px] bg-gray-100 rounded-full flex items-center justify-center hover:bg-gray-200 mr-3"
                                        >
                                            <img src="./images/view-icon.svg" alt="View" width={48} height={48} />
                                        </button>                                        
                                    )}
                                    {can.post_incident_edit && (
                                        <button
                                            onClick={() => handleOpenModal("edit",postIncident)}
                                            className="w-[45px] h-[45px] bg-gray-100 rounded-full flex items-center justify-center hover:bg-gray-200 mr-3"
                                        >
                                            <img src="./images/edit-icon.svg" alt="Edit" width={45} height={45} />
                                        </button>
                                    )}
                                    {can.post_incident_delete && (
                                        <button
                                            onClick={() => handleDelete(postIncident)}
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
                <Pagination links={postIncidents?.links} />
            </div>            
            {/* Modal */}
            <PostIncidentModal
                auth={auth}
                isOpen={isModalOpen}
                onClose={handleCloseModal}
                mode={modalMode}
                site={auth?.team?.name ?? 'Site Name'}
                post_incident_schedules={post_incident_schedules}
                sites={sites}
                users={users}
                postIncident={selectedPostIncident}
            />
            </div>

        </AuthenticatedLayout>
    );
};

export default PostIncidentsIndex;

import React, { useEffect, useState } from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link, router, useForm , usePage } from "@inertiajs/react";
import Pagination from '@/Components/Pagination';
import ElementModal from './ElementModal';
import SearchBar from '@/Components/SearchBar';
import AddButton from '@/Components/AddButton';
import EquipmentModal from './EquipmentModal';

const CategoryItemsIndex = ({auth,data, categoryItems, type_inspections, teams, type_bools, can, searchTerm, sortColumn, sortDirection }) => {	
    //alert(data);
    //const { auth } = usePage().props;
    //const { destroy } = useForm();
    console.log('data ', data);
    console.log('auth on categoryitem ', auth);
    console.log('categoryItems ', categoryItems);
    const [search, setSearch] = useState(searchTerm);
    const [isModalEquipmentOpen, setIsModalEquipmentOpen] = useState(false);
    const [isModalElementOpen, setIsModalElementOpen] = useState(false);
    const [modalMode, setModalMode] = useState("create");
    const [selectedElementItem, setSelectedElementItem] = useState(null);
    const [selectedEquipmentItem, setSelectedEquipmentItem] = useState(null);

    const handleOpenModal = (mode, categoryItem = null) => {
        console.log('mode ', mode);
        console.log('categoryitems ', categoryItem);
        setModalMode(mode);
        if(data == 1){
            setSelectedElementItem(categoryItem);
            setIsModalEquipmentOpen(true);            
        }else{
            setSelectedEquipmentItem(categoryItem);
            setIsModalElementOpen(true);
        }
    };

    const handleCloseModal = () => {
        setIsModalEquipmentOpen(false);
        setIsModalElementOpen(false);
        setSelectedElementItem(null);
        setSelectedEquipmentItem(null);
    };

    const handleSearchChange = (e) => {
        console.log('value ', e.target.value);
        setSearch(e.target.value);
    };

    const handleSearchSubmit = (e) => {
        e.preventDefault();
        // Redirect to the route with search query and other parameters
        console.log('search ', search);
        window.location.href = route('category-items.index', {
            data: data,
            search,
            sort: sortColumn,
            direction: sortDirection
        });
    };

    const handleDelete = (categoryItem) => {
        if (confirm("Are you sure you want to delete this Category Item?")) {
            //post(route("categoryitems.destroy", categoryitem), { _method: "DELETE",});
            router.delete(route('category-items.destroy', categoryItem), {preserveScroll: true});
        }
    };

    return (
        <AuthenticatedLayout user={auth.user} is_admin={auth?.is_admin}>
            <Head title="Items" />
            <div className="container mx-auto py-6">
            {/* Header */}
            <div className="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-6">
                <SearchBar search={search} handleSearchChange={(e) => handleSearchChange(e)} handleSearchSubmit={handleSearchSubmit} />
                {can.category_item_create && (
                <AddButton title='Add New Item' icon="./images/add-icon.svg" onClick={() => handleOpenModal("create")} />
                )}
            </div>
            <header className="mb-6 flex items-center justify-between w-[95%]">
                <div className="flex flex-col  items-right">               
                    <h1 className="view-header">{data == 1 ? 'Elements ' : 'Equipment'} <small>{auth?.team?.name ?? 'Site Name'}</small></h1>                   
                </div>
            </header>

            {/* CategoryItems Table */}
            <div className="overflow-x-auto bg-white rounded-lg">
                <table className="min-w-full divide-y divide-gray-200">
                    <thead className="bg-gray-50">
                        <tr>
                            <th className="px-6 py-3 table-header uppercase tracking-wider">
                            <Link 
                                href={route('category-items.index', { 
                                    data: data,
                                    sort: 'name', 
                                    direction: sortColumn === 'name' && sortDirection === 'asc' ? 'desc' : 'asc' 
                                })} 
                                className="flex flex-row justify-between items-center"
                            >
                                Category Name
                                <img src="./images/left-arrow-icon.svg" alt="filter" className="w-[16px] h-[15px] rounded-full ml-4" />
                            </Link>
                            </th>
                            <th className="px-6 py-3 table-header uppercase tracking-wider">
                            <Link 
                                href={route('category-items.index', { 
                                    data: data,
                                    sort: 'site_id', 
                                    direction: sortColumn === 'site_id' && sortDirection === 'asc' ? 'desc' : 'asc' 
                                })} 
                                className="flex flex-row justify-between items-center"
                            >
                                Site Name
                                <img src="./images/left-arrow-icon.svg" alt="filter" className="w-[16px] h-[15px] rounded-full ml-4" />
                            </Link>
                            </th>
                            <th className="px-6 py-3 table-header uppercase tracking-wider">
                            <Link 
                                href={route('category-items.index', { 
                                    data: data,
                                    sort: 'type_inspection_id', 
                                    direction: sortColumn === 'type_inspection_id' && sortDirection === 'asc' ? 'desc' : 'asc' 
                                })} 
                                className="flex flex-row justify-between items-center"
                            >
                                Inspection Type 
                                <img src="./images/left-arrow-icon.svg" alt="filter" className="w-[16px] h-[15px] rounded-full ml-4" />
                            </Link>
                            </th>
                            <th className="px-6 py-3 text-left table-header uppercase tracking-wider">
                                    Certification Recieved
                            </th>
                            <th className="px-6 py-3 uppercase tracking-wider">
                                
                            </th>
                        </tr>
                    </thead>
                    <tbody className="bg-white divide-y divide-gray-200">
                        {categoryItems?.data.map((categoryItem) => (
                            <tr key={categoryItem.id} className='h-[78px]' style={{borderBottomWidth: '12px', borderBottomColor: "#F8F8F8",}}>
                                <td className=" px-6 py-4 whitespace-nowrap text-start justify-center   ">
                                    <div className="w-full h-[49px] flex-col justify-center items-start gap-[9px] inline-flex border-r border-gray-300 ">
                                            <div className="w-full table-text1 ">{categoryItem.name}</div>                                        
                                    </div>
                                </td>
                                <td className=" px-6 py-4 whitespace-nowrap text-start justify-center   ">
                                    <div className="w-full h-[49px] flex-col justify-center items-start gap-[9px] inline-flex border-r border-gray-300 ">
                                            <div className="w-full table-text1 ">{categoryItem.site.name}</div>                                        
                                    </div>
                                </td>
                                <td className=" px-6 py-4 whitespace-nowrap text-start justify-center   ">
                                    <div className="w-full h-[49px] flex-col justify-center items-start gap-[9px] inline-flex border-r border-gray-300 ">
                                            <div className="w-full table-text1 ">{categoryItem.type.name}</div>                                        
                                    </div>
                                </td>
                                <td className=" px-6 py-4 whitespace-nowrap text-start justify-center   ">
                                    <div className="w-full h-[49px] flex-col justify-center items-start gap-[9px] inline-flex border-r border-gray-300 ">
                                            <div className="w-full table-text1 ">{categoryItem.certificate_recieved.name}</div>                                        
                                    </div>
                                </td>
                                <td className="px-6 py-4 flex flex-row items-center justify-end">
                                    {can.category_item_show && (
                                        <button
                                            onClick={() => handleOpenModal("view",categoryItem)}
                                            className="w-[45px] h-[45px] bg-gray-100 rounded-full flex items-center justify-center hover:bg-gray-200 mr-3"
                                        >
                                            <img src="./images/view-icon.svg" alt="View" width={48} height={48} />
                                        </button>                                        
                                    )}
                                    {can.category_item_edit && (
                                        <button
                                            onClick={() => handleOpenModal("edit",categoryItem)}
                                            className="w-[45px] h-[45px] bg-gray-100 rounded-full flex items-center justify-center hover:bg-gray-200 mr-3"
                                        >
                                            <img src="./images/edit-icon.svg" alt="Edit" width={45} height={45} />
                                        </button>
                                    )}
                                    {can.category_item_delete && (
                                        <button
                                            onClick={() => handleDelete(categoryItem)}
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
                <Pagination links={categoryItems?.links} dataParam={data} />
            </div>            
            {/* Modal */}
            {/* <CategoryItemModal
                isOpen={isModalOpen}
                onClose={handleCloseModal}
                mode={modalMode}
                categoryItem={selectedCategoryItem}
                type_inspections={type_inspections}
                teams={teams}
                type_bools={type_bools}
                team={auth?.team}
                type_item={data}
            /> */}
            <ElementModal
                isOpen={isModalEquipmentOpen}
                onClose={handleCloseModal}
                mode={modalMode}
                categoryItem={selectedElementItem}
                type_inspections={type_inspections}
                teams={teams}
                type_bools={type_bools}
                team={auth?.team}
                type_item={data}
            />
            <EquipmentModal
                isOpen={isModalElementOpen}
                onClose={handleCloseModal}
                mode={modalMode}
                equipmentItem={selectedEquipmentItem}
                type_inspections={type_inspections}
                type_bools={type_bools}
                team={auth?.team}
            />
            </div>

        </AuthenticatedLayout>
    );
};

export default CategoryItemsIndex;

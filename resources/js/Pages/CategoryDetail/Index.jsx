import React, { useEffect, useState } from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link, router, useForm , usePage } from "@inertiajs/react";
import Pagination from '@/Components/Pagination';
import CategoryDetailModal from './CategoryDetailModal';
import SearchBar from '@/Components/SearchBar';
import AddButton from '@/Components/AddButton';

const CategoryDetailsIndex = ({auth, data, categoryItems,categoryDetails, can, searchTerm, sortColumn, sortDirection }) => {
    //const { auth } = usePage().props;
    //const { destroy } = useForm();
    console.log('auth on categorydetail ', auth);
    console.log('categoryItems ', categoryItems);
    console.log('categoryDetails ', categoryDetails);
    const [search, setSearch] = useState(searchTerm);
    const [isModalOpen, setIsModalOpen] = useState(false);
    const [modalMode, setModalMode] = useState("create");
    const [selectedCategoryDetail, setSelectedCategoryDetail] = useState(null);

    const handleOpenModal = (mode, categorydetail = null) => {
        console.log('mode ', mode);
        console.log('categorydetails ', categorydetail);
        setModalMode(mode);
        setSelectedCategoryDetail(categorydetail);
        setIsModalOpen(true);
    };

    const handleCloseModal = () => {
        setIsModalOpen(false);
        setSelectedCategoryDetail(null);
    };

    const handleSearchChange = (e) => {
        console.log('value ', e.target.value);
        setSearch(e.target.value);
    };

    const handleSearchSubmit = (e) => {
        e.preventDefault();
        // Redirect to the route with search query and other parameters
        console.log('search ', search);
        window.location.href = route('category-details.index', {
            data: data,
            search,
            sort: sortColumn,
            direction: sortDirection
        });
    };

    const handleDelete = (categorydetail) => {
        if (confirm("Are you sure you want to delete this categorydetail?")) {
            //post(route("categorydetails.destroy", categorydetail), { _method: "DELETE",});
            router.delete(route('category-details.destroy', categorydetail), {preserveScroll: true});
        }
    };

    return (
        <AuthenticatedLayout user={auth.user} is_admin={auth?.is_admin}>
            <Head title="ItemDetails" />
            <div className="container mx-auto py-6">
            {/* Header */}
            <div className="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-6">
                <SearchBar search={search} handleSearchChange={(e) => handleSearchChange(e)} handleSearchSubmit={handleSearchSubmit} />
                {can.category_detail_create && (
                <AddButton title='Add New Detail' icon="./images/add-icon.svg" onClick={() => handleOpenModal("create")} />
                )}
            </div>
            <header className="mb-6 flex items-center justify-between w-[95%]">
                <div className="flex flex-col  items-right">
                    <h1 className="view-header">Item Details <small>{auth?.team?.name ?? 'Site Name'}</small></h1>                    
                </div>
            </header>

            {/* CategoryDetails Table */}
            <div className="overflow-x-auto bg-white rounded-lg">
                <table className="min-w-full divide-y divide-gray-200">
                    <thead className="bg-gray-50">
                        <tr>
                            <th className="px-6 py-3 table-header uppercase tracking-wider">
                            <Link 
                                href={route('category-details.index', { 
                                    data: data,
                                    sort: 'category_item_id', 
                                    direction: sortColumn === 'category_item_id' && sortDirection === 'asc' ? 'desc' : 'asc' 
                                })} 
                                className="flex flex-row justify-between items-center"
                            >
                                Item Name
                                <img src="./images/left-arrow-icon.svg" alt="filter" className="w-[16px] h-[15px] rounded-full ml-4" />
                            </Link>
                            </th>
                            <th className="px-6 py-3 table-header uppercase tracking-wider">
                            <Link 
                                href={route('category-details.index', { 
                                    sort: 'name', 
                                    direction: sortColumn === 'name' && sortDirection === 'asc' ? 'desc' : 'asc' 
                                })} 
                                className="flex flex-row justify-between items-center"
                            >
                                Detail Name
                                <img src="./images/left-arrow-icon.svg" alt="filter" className="w-[16px] h-[15px] rounded-full ml-4" />
                            </Link>
                            </th>
                            <th className="px-6 py-3 uppercase tracking-wider">
                                
                            </th>
                        </tr>
                    </thead>
                    <tbody className="bg-white divide-y divide-gray-200">
                        {categoryDetails?.data.map((categoryDetail) => (
                            <tr key={categoryDetail.id} className='h-[78px]' style={{borderBottomWidth: '12px', borderBottomColor: "#F8F8F8",}}>
                                <td className=" px-6 py-4 whitespace-nowrap text-start justify-center   ">
                                    <div className="w-full h-[49px] flex-col justify-center items-start gap-[9px] inline-flex border-r border-gray-300 ">
                                            <div className="w-full table-text1 ">{categoryDetail.category_item.name}</div>                                        
                                    </div>
                                </td>
                                <td className=" px-6 py-4 whitespace-nowrap text-start justify-center   ">
                                    <div className="w-full h-[49px] flex-col justify-center items-start gap-[9px] inline-flex border-r border-gray-300 ">
                                            <div className="w-full table-text1 ">{categoryDetail.name}</div>                                        
                                    </div>
                                </td>
                                <td className="px-6 py-4 flex flex-row items-center justify-end">
                                    {can.category_detail_show && (
                                        <button
                                            onClick={() => handleOpenModal("view",categoryDetail)}
                                            className="w-[45px] h-[45px] bg-gray-100 rounded-full flex items-center justify-center hover:bg-gray-200 mr-3"
                                        >
                                            <img src="./images/view-icon.svg" alt="View" width={48} height={48} />
                                        </button>                                        
                                    )}
                                    {can.category_detail_edit && (
                                        <button
                                            onClick={() => handleOpenModal("edit",categoryDetail)}
                                            className="w-[45px] h-[45px] bg-gray-100 rounded-full flex items-center justify-center hover:bg-gray-200 mr-3"
                                        >
                                            <img src="./images/edit-icon.svg" alt="Edit" width={45} height={45} />
                                        </button>
                                    )}
                                    {can.category_detail_delete && (
                                        <button
                                            onClick={() => handleDelete(categoryDetail)}
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
                <Pagination links={categoryDetails?.links} />
            </div>            
            {/* Modal */}
            <CategoryDetailModal
                isOpen={isModalOpen}
                onClose={handleCloseModal}
                mode={modalMode}
                categoryDetail={selectedCategoryDetail}
                categoryItems={categoryItems}
                team={auth?.team}
            />
            </div>

        </AuthenticatedLayout>
    );
};

export default CategoryDetailsIndex;

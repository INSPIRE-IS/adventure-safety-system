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




const InspectionsIndex = ({auth, inspection, category_item, inspection_details, can, users,inspection_id,category_id }) => {
const [isManageModalOpen, setIsManageModalOpen] = useState(false);
const [modalMode, setModalMode] = useState("create");
const [checkedState, setCheckedState] = useState([]);
console.log('inspection_details',inspection_details);
console.log('inspection',inspection);
console.log('category_item',category_item);
const categorized = inspection_details.reduce(
    (acc, item) => {
      if (item.condition === 0) acc.good.push(item);
      else if (item.condition === 1) acc.worn.push(item);
      else if (item.condition === 2) acc.failed.push(item);
      return acc;
    },
    { good: [], worn: [], failed: [] }
  );

  const totalChecked = inspection_details.length;
  const goodCount = categorized.good.length;
  const wornCount = categorized.worn.length;
  const failedCount = categorized.failed.length;


const handleOpenManageModal = (mode) => {

    if(inspection_id == null || category_id == null){
        alert("Please select at least one inspection");
        return;
    }
    setModalMode(mode);
    setCheckedState({inspection_id,category_id});
    setIsManageModalOpen(true);
};

const handleCloseModal = () => {
    setIsManageModalOpen(false);
};



    return (
        <AuthenticatedLayout user={auth.user}>
            <Head title="Inspections" />
            <div className="container mx-auto py-6">
                {/* Header */}
                <div className="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-6">
                    <EditButton title='Back to Inspection Management' width="w-[254px]" height="h-[52px]" icon="./images/back-icon.svg"  
                    onClick={() => router.get(route('equipments.index'))} />
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
                <div className=" pl-[40px] pr-[32px] pt-5 rounded-3xl bg-white">
                    <div className="p-6 bg-gray-100 min-h-screen">
                        <div className="grid grid-cols-3 gap-4 items-center mb-6">
                            <h2 className="label-header-4-18">{category_item?.name}</h2>

                            <p className="view-header-2">Inspector: <br/><span className="label-sub-header-4">{inspection?.assigned_to?.name}</span></p>
                            <p className="view-header-2">Last Date Completed: <br/><span className="label-sub-header-4">{inspection?.complete_date?.split(" ")[0]}</span></p>

                        </div>

                        {/* Summary Cards */}
                        <div className="grid grid-cols-4 gap-4 mb-6">
                            <div className="bg-[#3E3E4B] w=[227px] h-[158px] pt-[25px] pl-[30px] rounded-lg shadow-md">
                            <p className="label-sub-header-4-white">Total Checked</p>
                            <p className="label-header-4-yellow">{totalChecked}</p>
                            </div>
                            <div className="bg-[#C8E6C9] w=[227px] h-[158px] pt-[25px] pl-[30px] rounded-lg shadow-md">
                            <p className="label-sub-header-4-white">Good</p>
                            <p className="label-header-4-black">{goodCount}</p>
                            </div>
                            <div className="bg-[#EBA53566] w=[227px] h-[158px] pt-[25px] pl-[30px] rounded-lg shadow-md">
                            <p className="label-sub-header-4-white">Worn but OK</p>
                            <p className="label-header-4-black">{wornCount}</p>
                            </div>
                            <div className="bg-[#FFC7C7] w=[227px] h-[158px] pt-[25px] pl-[30px] rounded-lg shadow-md">
                            <p className="label-sub-header-4-white">Failed and Removed</p>
                            <p className="label-header-4-black">{failedCount}</p>
                            </div>
                        </div>

                        {/* Table */}
                        <div className="bg-white p-4 rounded-lg shadow-md">



                                            <table className="min-w-full divide-y divide-gray-200">
                                            <thead className="bg-white ">
                                                    <tr className="w-[100%]">
                                                        <th className="px-6 py-3 text-start table-header2 uppercase tracking-wider  w-[15%]">
                                                        Title
                                                        </th>
                                                        <th className="px-6 py-3 table-header2 uppercase tracking-wider  w-[15%]">
                                                        Status
                                                        </th>
                                                        <th className="px-6 py-3 table-header2 uppercase tracking-wider  w-[15%]">
                                                        Last Checked
                                                        </th>
                                                        <th className="px-6 py-3 uppercase tracking-wider  w-[30%]">
                                                            
                                                        </th>
                                                    </tr>
                                                </thead>
                                                <tbody className="bg-white divide-y divide-gray-200">
                                                    {inspection_details?.map((item) => {
                                                        console.log("inspection", item);

                            
                            
                                                    return(
                                                        <tr key={item.id} className='h-[78px] w-full ' style={{borderBottomWidth: '12px', borderBottomColor: "#F8F8F8",}}>   

                                                            <td className=" px-6 py-4 whitespace-nowrap items-center justify-center ">
                            

                                                                <div className="w-[175px] h-[49px] flex-row justify-start items-start gap-[9px] inline-flex border-r border-gray-300 ">
                                                                    {item.condition === 0 ? (
                                                                        <svg width="35" height="35" viewBox="0 0 42 42" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                            <g id="Union/New value">
                                                                                <path id="Subtract" fill-rule="evenodd" clip-rule="evenodd" d="M20.7903 42C32.2724 42 41.5805 32.6919 41.5805 21.2097C41.5805 9.72756 32.2724 0.419434 20.7903 0.419434C9.30812 0.419434 0 9.72756 0 21.2097C0 32.6919 9.30812 42 20.7903 42ZM28.9757 14.1978C29.8084 14.848 29.9433 16.0341 29.2774 16.847L18.9813 29.4124C18.3299 30.2072 17.1497 30.3538 16.3155 29.7428L11.1675 25.9732C10.3145 25.3486 10.1417 24.1672 10.7814 23.3345C11.4211 22.5017 12.6311 22.3329 13.4841 22.9575L17.1352 25.631L26.2624 14.4921C26.9284 13.6793 28.1433 13.5475 28.9757 14.1978Z" fill="#37b36f"/>
                                                                            </g>
                                                                        </svg>
                                                                    ) : (
                                                                        <svg width="35" height="35" viewBox="0 0 42 42" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                            <path id="Subtract" fill-rule="evenodd" clip-rule="evenodd" d="M20.7903 41.7903C32.2724 41.7903 41.5805 32.4821 41.5805 21C41.5805 9.51784 32.2724 0.209717 20.7903 0.209717C9.30812 0.209717 0 9.51784 0 21C0 32.4821 9.30812 41.7903 20.7903 41.7903ZM28.9579 16.0995C29.8601 15.1974 29.8601 13.7347 28.9579 12.8325C28.0558 11.9303 26.593 11.9303 25.6909 12.8325L20.7903 17.7331L15.8898 12.8325C14.9876 11.9303 13.5249 11.9303 12.6227 12.8325C11.7205 13.7347 11.7205 15.1974 12.6227 16.0995L17.5233 21.0001L12.6227 25.9007C11.7206 26.8028 11.7205 28.2655 12.6227 29.1677C13.5249 30.0699 14.9876 30.0698 15.8898 29.1677L20.7903 24.2671L25.6909 29.1677C26.593 30.0698 28.0558 30.0699 28.9579 29.1677C29.8601 28.2655 29.8601 26.8028 28.9579 25.9007L24.0574 21.0001L28.9579 16.0995Z" fill="#FF0000"/>
                                                                        </svg>
                                                                    )}
                                                                    <span className='label-header-4-alt mt-2'>{category_item?.name} {item.category_detail.name}</span>                                          
                                                                </div>
                            
                                                            </td>
                                                            <td className=" px-6 py-4 whitespace-nowrap text-start justify-center table-text1 ">
                                                                
                                                                <div className="w-[180px] h-[49px] flex-col justify-start items-start gap-[9px] inline-flex  border-r border-gray-300 ">
                                                                    <div className="w-full label-comment-text mt-2">
                                                                        {item.condition === 0 && "Good"}
                                                                        {item.condition === 1 && "Worn but OK"}
                                                                        {item.condition === 2 && "Failed and Removed"}
                                                                    </div>
                                                                                                 
                                                                </div>
                                                            </td>


                                                            <td className=" px-6 py-4 whitespace-nowrap text-center justify-center   table-text1   ">
                            
                                                                <div className="w-[175px] h-[49px] flex-col justify-center items-start gap-[9px] inline-flex border-r border-gray-300 ">
                                                                    <div className="w-full label-comment-text mt-2">
                                                                        {item?.created_at?.split(" ")[0]}
                                                                    </div>                                        
                                                                </div>
                                                                 
                                                            </td>

                          


                                                            <td className="px-6 py-4 flex flex-row items-center justify-start ">

                                                                        {item.condition === 2 ? (
                                                                            <div onClick={() => {alert('i need to do something');}}
                                                                            className="bg-[#FF0000] w-[214px] h-[52px] px-6 py-[9px] rounded-xl border border-[#d0d0d0] justify-center items-center gap-[126px] inline-flex">
                                                                                <div className="text-[#FFFFFF] text-base font-semibold font-['Manrope'] leading-normal">Replace with new</div>
                                                                            </div> 


                                                                        ) : (
                                                                            <div onClick={() => {alert('i need to do something');}}
                                                                            className="bg-[#FFFFFF] w-[214px] h-[52px] px-6 py-[9px] rounded-xl border border-[#d0d0d0] justify-center items-center gap-[126px] inline-flex">
                                                                                <div className="text-[#bfbfbf] text-base font-semibold font-['Manrope'] leading-normal">Replace with new</div>
                                                                            </div> 
                                                                        )}

                                                            </td>
                                                        </tr>
                                                    )})}
                                                </tbody>
                                            </table>
                        </div>
                    </div>


                </div>
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

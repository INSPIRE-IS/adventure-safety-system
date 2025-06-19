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
import EditButton2 from '@/Components/EditButton2';
import lookupIcon from '@/Utils/lookupIcons';




const InspectionsIndex = ({auth, inspection, category_item, inspection_details, can, users,inspection_id,category_id }) => {
const [isManageModalOpen, setIsManageModalOpen] = useState(false);
const [modalMode, setModalMode] = useState("create");
const [checkedState, setCheckedState] = useState([]);
// console.log('inspection_details',inspection_details);
// console.log('inspection',inspection);
// console.log('category_item',category_item);
const [openTables, setOpenTables] = useState({});

const toggleTable = (itemId) => {
  setOpenTables((prev) => ({
    ...prev,
    [itemId]: !prev[itemId],
  }));
};

const groupedByCategoryItem = inspection_details.reduce((acc, item) => {
  const itemId = item.category_item.id;
  if (!acc[itemId]) {
    acc[itemId] = {
      name: item.category_item.name,
      items: [],
      good: 0,
      worn: 0,
      failed: 0,
    };
  }
  acc[itemId].items.push(item);
  if (item.condition === 0) acc[itemId].good++;
  else if (item.condition === 1) acc[itemId].worn++;
  else if (item.condition === 2) acc[itemId].failed++;
  return acc;
}, {});

console.log('groupedByCategoryItem',groupedByCategoryItem);

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

const handleReplace = (item_id,id) => {
    console.log('inspection_id',inspection_id);
    console.log('category_id',category_id);
    console.log('item_id',item_id);
    console.log('id',id);
const data = {
        inspection_id: inspection_id,
        category_id: category_id,
        item_id: item_id,
        id: id,
    };

    router.post(route("equipments.replace",data), {
        onSuccess: () => {
            reset();
            onClose();
        },
    });

};



    return (
        <AuthenticatedLayout user={auth.user} is_admin={auth?.is_admin}>
            <Head title="Inspections" />
            <div className="container mx-auto py-6">
                {/* Header */}
                <div className="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-6">
                <EditButton2
                    title="Back to Inspection Management" 
                    width="w-[254px]" 
                    height="h-[52px]" 
                    icon={lookupIcon("circleLeft")} 
                    onClick={() => router.get(route('equipments.index'))}
                />
                </div>
                <header className="mb-6 flex items-center justify-between w-[100%]">
                    <div className="flex flex-col  items-right">               
                        <h1 className="view-header">{auth?.team?.name ?? 'Site Name'}</h1>                    
                    </div>
                    <div className="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-6">
                        {can.inspection_edit_reminder && (
                        <EditButton2 title='Send Reminder' width="w-[214px]" height="h-[52px]" icon={lookupIcon("add_alert_clock")} onClick={() => handleOpenManageModal("reminder")} />
                        )}
                        {can.inspection_edit_inspector && (
                        <EditButton2 title='Assign New Inspector' width="w-[244px]" height="h-[52px]" icon={lookupIcon("add_user")} onClick={() => handleOpenManageModal("assign")} />
                        )}
                        {can.inspection_edit_date && (
                        <EditButton2 title='Update Inspection Date' width="w-[254px]" height="h-[52px]" icon={lookupIcon("clock_setting_gear")} onClick={() => handleOpenManageModal("change")} />
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
{Object.entries(groupedByCategoryItem).map(([categoryItemId, group]) => {
  const isOpen = openTables[categoryItemId];
  const errorCount = group.items.filter(item => item.condition > 0).length;

  return (
    <div key={categoryItemId} className="mb-8 border rounded-lg">
      <button
        onClick={() => toggleTable(categoryItemId)}
        className="w-full flex justify-between items-center px-4 py-3 bg-gray-100 hover:bg-gray-200"
      >
        <span className="label-header-4">{group.name}</span>
        <div className="flex items-center gap-4">
                <span
                  className={`text-xs font-semibold px-2 py-1 rounded-full ${
                    group.items.length > 0 ? 'bg-[#3e3e4b] text-[#f6c546]' : 'bg-gray-100 text-gray-600'
                  }`}
                >
                   Total {group.items.length}
                </span>
                <span
                  className={`text-xs font-semibold px-2 py-1 rounded-full ${
                    group.good > 0 ? 'bg-[#C8E6C9] text-black' : 'bg-gray-100 text-gray-600'
                  }`}
                >
                  {group.good} Good
                </span>
                <span
                  className={`text-xs font-semibold px-2 py-1 rounded-full ${
                    group.worn > 0 ? 'bg-[#EBA53566] text-black' : 'bg-gray-100 text-gray-600'
                  }`}
                >
                  {group.worn} Worn
                </span>

                <span
                  className={`text-xs font-semibold px-2 py-1 rounded-full ${
                    group.failed > 0 ? 'bg-[#FFC7C7] text-black' : 'bg-gray-100 text-gray-600'
                  }`}
                >
                  {group.failed} Failed
                </span>
          <svg
            className={`w-5 h-5 transform transition-transform ${
              isOpen ? 'rotate-90' : ''
            }`}
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
          >
            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M9 5l7 7-7 7" />
          </svg>
        </div>
      </button>

      {isOpen && (
        <div className="p-4">
          <InspectionTable items={group.items} handleReplace={handleReplace} />
        </div>
      )}
    </div>
  );
})}


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

const InspectionTable = ({ items, handleReplace }) => {
  return (
    <div className="bg-white px-3 border-b-4 border-gray-100">
      <table className="min-w-full divide-y divide-gray-200">
        <thead className="bg-white ">
            <tr className="w-[100%]">
                <th className="px-6 py-3 text-start table-header2 uppercase tracking-wider  w-[180px]">
                    Title
                </th>
                <th className="px-6 py-3 text-start table-header2 uppercase tracking-wider  w-[180px]">
                    Status
                </th>
                <th className="px-6 py-3 text-start table-header2 uppercase tracking-wider  w-[175px]">
                    Last Checked
                </th>
                <th className="px-6 py-3 uppercase tracking-wider  w-[214px]">
                </th>
            </tr>
        </thead>
        <tbody className="bg-white divide-y divide-gray-200">
          {items.map(item => (
            <tr key={item.id} className='h-[78px] w-full ' style={{borderBottomWidth: '12px', borderBottomColor: "#F8F8F8",}}>
            <td className=" px-6 py-4 whitespace-nowrap items-center justify-center ">
                <div className="w-[175px] h-[49px] flex-row justify-start items-start gap-[9px] inline-flex border-r border-gray-300 ">
                    {item.condition === 0 ? (
                        <svg width="35" height="35" viewBox="0 0 42 42" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <g id="Union/New value">
                                <path id="Subtract" fillRule="evenodd" clipRule="evenodd" d="M20.7903 42C32.2724 42 41.5805 32.6919 41.5805 21.2097C41.5805 9.72756 32.2724 0.419434 20.7903 0.419434C9.30812 0.419434 0 9.72756 0 21.2097C0 32.6919 9.30812 42 20.7903 42ZM28.9757 14.1978C29.8084 14.848 29.9433 16.0341 29.2774 16.847L18.9813 29.4124C18.3299 30.2072 17.1497 30.3538 16.3155 29.7428L11.1675 25.9732C10.3145 25.3486 10.1417 24.1672 10.7814 23.3345C11.4211 22.5017 12.6311 22.3329 13.4841 22.9575L17.1352 25.631L26.2624 14.4921C26.9284 13.6793 28.1433 13.5475 28.9757 14.1978Z" fill="#37b36f"/>
                            </g>
                        </svg>
                    ) : (
                        <svg width="35" height="35" viewBox="0 0 42 42" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path id="Subtract" fillRule="evenodd" clipRule="evenodd" d="M20.7903 41.7903C32.2724 41.7903 41.5805 32.4821 41.5805 21C41.5805 9.51784 32.2724 0.209717 20.7903 0.209717C9.30812 0.209717 0 9.51784 0 21C0 32.4821 9.30812 41.7903 20.7903 41.7903ZM28.9579 16.0995C29.8601 15.1974 29.8601 13.7347 28.9579 12.8325C28.0558 11.9303 26.593 11.9303 25.6909 12.8325L20.7903 17.7331L15.8898 12.8325C14.9876 11.9303 13.5249 11.9303 12.6227 12.8325C11.7205 13.7347 11.7205 15.1974 12.6227 16.0995L17.5233 21.0001L12.6227 25.9007C11.7206 26.8028 11.7205 28.2655 12.6227 29.1677C13.5249 30.0699 14.9876 30.0698 15.8898 29.1677L20.7903 24.2671L25.6909 29.1677C26.593 30.0698 28.0558 30.0699 28.9579 29.1677C29.8601 28.2655 29.8601 26.8028 28.9579 25.9007L24.0574 21.0001L28.9579 16.0995Z" fill="#FF0000"/>
                        </svg>
                    )}
                    <span className='label-header-4-alt mt-2'>{item.category_detail.name}</span>                                          
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
            <td className=" px-6 py-4 whitespace-nowrap text-start justify-center   table-text1   ">
                <div className="w-[175px] h-[49px] flex-col justify-center items-start gap-[9px] inline-flex border-r border-gray-300 ">
                    <div className="w-full label-comment-text mt-2">
                        {item?.created_at?.split(" ")[0]}
                    </div>                                        
                </div>
            </td>
            <td className="px-6 py-4 flex flex-row items-center justify-start ">
            {item.condition === 2 ? (
                <div onClick={() => handleReplace(item.id,1)}
                className="bg-[#FF0000] w-[214px] h-[52px] px-6 py-[9px] rounded-xl border border-[#d0d0d0] justify-center items-center gap-[126px] inline-flex">
                    <div className="text-[#FFFFFF] text-base font-semibold font-['Manrope'] leading-normal">Replace with new</div>
                </div> 
            ) : (
                <div onClick={() => handleReplace(item.id,2)}
                className="bg-[#FFFFFF] w-[214px] h-[52px] px-6 py-[9px] rounded-xl border border-[#d0d0d0] justify-center items-center gap-[126px] inline-flex">
                    <div className="text-[#bfbfbf] text-base font-semibold font-['Manrope'] leading-normal">Replace with new</div>
                </div> 
            )}
            </td>
            </tr>
          ))}
        </tbody>
      </table>
    </div>
  );
};
import SearchBar from '@/Components/SearchBar';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import lookupIcon from '@/Utils/lookupIcons';

import { Head } from '@inertiajs/react';
import { useState } from 'react';

const asArray = (value) => {
  if (Array.isArray(value)) return value;
  if (value && typeof value === 'object') return Object.values(value);
  return [];
};

export default function Dashboard({ auth, cardData, searchTerm, sortColumn, sortDirection }) {
  console.log('auth dashboard', auth);
  console.log('auth dashboard', cardData);
  const [search, setSearch] = useState(searchTerm);
  const safeCardData = asArray(cardData);
  const looksLikeAdminData = safeCardData.some((site) => Array.isArray(site?.entries));
  const showAdminCards = auth?.is_admin === true || (looksLikeAdminData && auth?.is_admin !== false);

    const handleSearchChange = (e) => {
      console.log('value ', e.target.value);
      setSearch(e.target.value);
  };

  const handleSearchSubmit = (e) => {
      e.preventDefault();
      // Redirect to the route with search query and other parameters
      console.log('search ', search);
      window.location.href = route('dashboard', {
          search,
          sort: sortColumn,
          direction: sortDirection
      });
  };



    return (
        <AuthenticatedLayout
            user={auth.user}
            is_admin={auth?.is_admin}
            header={<h2 className="font-semibold text-xl text-gray-800 leading-tight">Dashboard</h2>}
        >
            <Head title="Dashboard" />
            <div className="flex flex-col mt-[46px] lg:flex-row lg:items-center lg:justify-between  gap-6 p-4">
            {showAdminCards ? (<div className='view-header'>Overview of all sites</div>) : 
              (<div className='view-header'>Welcome {auth?.user?.name}, these are your tasks</div>)}
                
                <SearchBar search={search} handleSearchChange={(e) => handleSearchChange(e)} handleSearchSubmit={handleSearchSubmit} />
            </div>
            {showAdminCards ? (<SiteCards1 sites={safeCardData} />) : (<SiteCards2 sites={safeCardData} />)}


        </AuthenticatedLayout>
    );
}


const SiteCards1 = ({ sites }) => {
    const safeSites = asArray(sites);
    return (
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 p-4">
        {safeSites.map((site, index) => (
          <div key={index} className="bg-white shadow-lg rounded-lg p-4">
            <h2 className="sidebar-header-2">{site.site_name}</h2>
            {(() => {
              const entries = asArray(site?.entries);
  
              if (entries.length === 0) {
                return <p className="text-gray-500 text-center">No records available</p>;
              }

              return entries.map((entry, i) => (
                <div key={i} className="bg-gray-100 rounded-lg p-3 mb-3">
                  <p className="body-heading-16">{entry.name}</p>
                  <div className='flex flex-row justify-between bg-white rounded-lg p-3 my-3 "'>
                    <div className='flex flex-col'>
                        <span className="table-header1">Date last conducted:</span>
                        <span className='table-header1'> {entry.complete_date?.split(" ")[0] || entry.due_date?.split(" ")[0]}</span>                    
                    </div>
                    <div>
                  <span
                      className={`pill1 px-3 py-1 rounded-full ${
                        entry.complete_date ? "bg-green-500" : "bg-red-500"
                      }`}
                    >
                      {entry.complete_date ? "COMPLETED" : "PENDING"}
                    </span>
                      </div>
  
                  </div>

                </div>
              ));
            })()}
          </div>
        ))}
      </div>
    );
  };

  const SiteCards2 = ({ sites }) => {
    const safeSites = asArray(sites);
    return (
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 p-4">
        {safeSites.map((site, index) =>
          asArray(site?.categories).map((category, catIndex) => (
            <div key={`${index}-${catIndex}`} className="bg-white shadow-lg rounded-lg p-6">
              <div className="flex justify-center items-center mb-4">
              {category.icon == 'sim' && (
                <div className='bg-[#ECECED] w-[86px] h-[86px] rounded-full flex items-center justify-center hover:bg-gray-200'>
                  {lookupIcon("inspections")}
                </div>
              )}
              {category.icon == 'sdm' && (
                <div className='bg-[#ECECED] w-[86px] h-[86px] rounded-full flex items-center justify-center hover:bg-gray-200'>
                  {lookupIcon("drills")}
                </div>
              )}
              {category.icon == 'pim' && (
                <div className='bg-[#ECECED] w-[86px] h-[86px] rounded-full flex items-center justify-center hover:bg-gray-200'>
                {lookupIcon("equipment")}
                </div>
              )}
              </div>
              <h2 className="sidebar-header-2 text-center mb-4">{category.title}</h2>
  
              {asArray(category?.entries).length === 0 ? (
                <p className="sidebar-header-2 text-center">No records available</p>
              ) : (
                asArray(category?.entries).map((entry, i) => (
                  <div key={i} className="bg-gray-100 rounded-lg p-3 mb-3 flex justify-between items-center">
                    <div>
                      <p className="view-sub-header">{entry.name}</p>
                      <p className="input-header">{entry.description}</p>
                    </div>
                    <span className="text-gray-400 text-lg">
                    {lookupIcon("circleRight")}

                    </span>
                  </div>
                ))
              )}
            </div>
          ))
        )}
      </div>
    );
  };

import React, { act, useEffect, useState } from 'react';
import { Link, router, useForm, usePage } from '@inertiajs/react';

  const navLinks = [
    { name: 'Staff', route: 'users.index', permission: "user_access" },
    { name: 'Inspection', route: 'inspections.index', permission: "inspection_access" },
    { name: 'PPE', route: 'equipments.index', permission: "inspection_access" },
    { name: 'Rescue Drills', route: 'drills.index', permission: "drill_access" },
    { name: 'Incident', route: 'incidents.index', permission: "incident_access" },
    { name: 'Complaint Management', route: 'complaints.index', permission: "complaint_access" },
    { name: 'Site Management', route: 'files.index', permission: "file_access" },
  ];
const Sidebar = () => {
    const { auth } = usePage().props;
    const sidebarLinks = [
            {
                label: "Dashboard",
                icon: "fas fa-tachometer-alt",
                route: "dashboard",
                permission: null, // No permission required
            },
            ...(auth?.roles?.includes('super')
                ? [{
                    label: "Pulse",
                    icon: "fas fa-heartbeat",
                    href: "/pulse",
                    permission: null,
                }]
                : []),
            {
                label: "Help Center",
                icon: "fas fa-question-circle",
                route: "help-center.index",
                permission: null, // Visible to all admin/sidebar users
            },
            {
                label: "Inspection",
                icon: "fas fa-users",
                permission: "inspection_access",
                route: 'inspections.index',
                dropdown: [
                    // {
                    //     label: "Type Cerfitications",
                    //     icon: "fas fa-file-signature",
                    //     route: "type-bools.index",
                    //     permission: "type_bool_access",
                    // },
                    // {
                    //     label: "Type Inspections",
                    //     icon: "fas fa-file-signature",
                    //     route: "type-inspections.index",
                    //     permission: "type_inspection_access",
                    // },
                    {
                        label: "Element Items",
                        icon: "fas fa-user",
                        route: "category-items.index",
                        param: { data: 1 },
                        permission: "category_item_access",
                    },
                    // {
                    //     label: "Element Details",
                    //     icon: "fas fa-bell",
                    //     route: "category-details.index",
                    //     param: { data: 1 },
                    //     permission: "category_detail_access",
                    // },
                    // {
                    //     label: "Reports",
                    //     icon: "fas fa-building",
                    //     route: "inspections.index",
                    //     permission: "inspection_access",
                    // },
                    {
                        label: "Schedules",
                        icon: "fas fa-building",
                        route: "inspection-schedules.index",
                        param: { data: 1 },
                        permission: "inspection_schedule_access",
                    },
                    // {
                    //     label: "Safety Inspection Details",
                    //     icon: "fas fa-file-alt",
                    //     route: "safety-inspection-details.index",
                    //     permission: "safety_inspection_detail_access",
                    // },
                ],
            },
            {
                label: "PPE",
                icon: "fas fa-users",
                permission: "inspection_access",
                route: 'equipments.index',
                dropdown: [
                    // { // this is working it is not implemented on app and on ppe report though and was not part of original scope
                    //     label: "Type Degradation",
                    //     icon: "fas fa-unlock-alt",
                    //     route: "type-degredations.index",
                    //     permission: "type_degradation_access",
                    // },
                    {
                        label: "Equipment",
                        icon: "fas fa-user",
                        route: "equipment-items.index",
                        param: { data: 3 },
                        permission: "category_item_access",
                    },
                    // {
                    //     label: "Equipment List",
                    //     icon: "fas fa-bell",
                    //     route: "category-details.index",
                    //     param: { data: 3 },
                    //     permission: "category_detail_access",
                    // },
                    // {
                    //     label: "Inspections",
                    //     icon: "fas fa-building",
                    //     route: "equipments.index",
                    //     permission: "inspection_access",
                    // },
                    {
                        label: "Schedules",
                        icon: "fas fa-building",
                        route: "equipment-schedules.index",
                        param: { data: 3 },
                        permission: "inspection_schedule_access",
                    },
                    // {
                    //     label: "Equipment Inspection Details",
                    //     icon: "fas fa-building",
                    //     route: "equipment-inspection-details.index",
                    //     permission: "equipment_inspection_detail_access",
                    // },
                ],
            },        
            {
                label: "Rescue Drills",
                icon: "fas fa-users",
                permission: "drill_access",
                route: 'drills.index',
                dropdown: [
                    {
                        label: "Type Drills",
                        icon: "fas fa-unlock-alt",
                        route: "type-drills.index",
                        permission: "type_drill_access",
                    },
                    {
                        label: "Schedules",
                        icon: "fas fa-briefcase",
                        route: "drill-schedules.index",
                        permission: "drill_schedule_access",
                    },            
                    // {
                    //     label: "Drill Reports",
                    //     icon: "fas fa-file-alt",
                    //     route: "drills.index",
                    //     permission: "drill_access",
                    // },  
                    // {
                    //     label: "Drill Details",
                    //     icon: "fas fa-file-alt",
                    //     route: "drill-details.index",
                    //     permission: "drill_detail_access",
                    // },                       
                    // {
                    //     label: "Drill Members",
                    //     icon: "fas fa-bell",
                    //     route: "drill-members.index",
                    //     permission: "drill_member_access",
                    // },            
                ],
            },
            {
                label: "Incident",
                icon: "fas fa-users",
                permission: "incident_access",
                route: 'incidents.index',
                dropdown: [
                    // {
                    //     label: "Incident Type",
                    //     icon: "fas fa-unlock-alt",
                    //     route: "type-incidents.index",
                    //     permission: "type_incident_access",
                    // },
                    // {
                    //     label: "Incident Reports",
                    //     icon: "fas fa-briefcase",
                    //     route: "incidents.index",
                    //     permission: "incident_access",
                    // },
                    // {
                    //     label: "Question Types",
                    //     icon: "fas fa-bell",
                    //     route: "type-questions.index",
                    //     permission: "type_question_access",
                    // },
                    {
                        label: "Post Incident Schedule",
                        icon: "fas fa-building",
                        route: "post-incident-schedules.index",
                        permission: "post_incident_schedule_access",
                    },            
                    {
                        label: "Post Incident",
                        icon: "fas fa-file-alt",
                        route: "post-incidents.index",
                        permission: "post_incident_access",
                    },
                    // {
                    //     label: "Post Incident Detail",
                    //     icon: "fas fa-file-alt",
                    //     route: "post-incident-details.index",
                    //     permission: "post_incident_detail_access",
                    // },
                ],
            },
            {
                label: "Site",
                icon: "fas fa-users",
                permission: "site_access",
                route: 'sites.index',
                dropdown: [
                    // {
                    //     label: "Type Files",
                    //     icon: "fas fa-unlock-alt",
                    //     route: "type-files.index",
                    //     permission: "type_file_access",
                    // },
                    {
                        label: "Documents",
                        icon: "fas fa-briefcase",
                        route: "files.index",
                        permission: "file_access",
                    },
                ],
            },
                        {
                label: "Staff",
                icon: "fas fa-users",
                permission: "user_access",
                route: 'users.index',
                dropdown: [
                    {
                        label: "Permissions",
                        icon: "fas fa-unlock-alt",
                        route: "permissions.index",
                        permission: "permission_access",
                    },
                    {
                        label: "Roles",
                        icon: "fas fa-briefcase",
                        route: "roles.index",
                        permission: "role_access",
                    },
                    // {
                    //     label: "Users",
                    //     icon: "fas fa-user",
                    //     route: "users.index",
                    //     permission: "user_access",
                    // },
                    // {
                    //     label: "Sites",
                    //     icon: "fas fa-building",
                    //     route: "sites.index",
                    //     permission: "site_access",
                    // },              
                    {
                        label: "User Alerts",
                        icon: "fas fa-bell",
                        route: "user-alerts.index",
                        permission: "user_alert_access",
                    },
                    {
                        label: "Audit Logs",
                        icon: "fas fa-file-alt",
                        route: "audit-logs.index",
                        permission: "audit_log_access",
                    },
                ],
            },
        // {
        //     label: "Calendar",
        //     icon: "fas fa-calendar",
        //     route: "calendar.index",
        //     permission: "file_access",
        // },
        // {
        //     label: "Change Password",
        //     icon: "fas fa-key",
        //     route: "password.update",
        //     permission: "change_password",
        // },
        // {
        //     label: "Logout",
        //     icon: "fas fa-sign-out-alt",
        //     route: "logout",
        //     //route: "href={route('logout')} method="post"",
        //    action: () => document.getElementById('logoutform').submit(),
        //    //action: handleSubmit,
        //    permission: null,
        // },
    ];       
    console.log('auth sidebar', auth);
    const [openSites, setOpenSites] = useState(() => {
        return localStorage.getItem('openSites') === 'true';
    });
    const [openParentSection, setOpenParentSection] = useState(() => {
    const stored = localStorage.getItem('openParentSection');
    return stored !== null ? parseInt(stored, 10) : null;
    });
    const [selectedSiteId, setSelectedSiteId] = useState(() => {
        return localStorage.getItem('selectedSiteId') || null;
    });
    const [selectedSubLink, setSelectedSubLink] = useState(() => {
        return localStorage.getItem('selectedSubLink') || null;
    });
 

    useEffect(() => {
    const storedSites = localStorage.getItem('openSites') === 'true';
    const storedParentSection = localStorage.getItem('openParentSection');
    const storedSiteId = localStorage.getItem('selectedSiteId');
    const storedSubLink = localStorage.getItem('selectedSubLink');

    if (storedSites) setOpenSites(true);
    if (storedSiteId) setSelectedSiteId(storedSiteId);
    if (storedSubLink) setSelectedSubLink(storedSubLink);

    // Only set open parent if we have a selected sublink
    if (storedSubLink && storedParentSection === null) {
        const parentIdx = sidebarLinks.findIndex(link =>
        link.dropdown?.some(sub => sub.route === storedSubLink)
        );
        if (parentIdx !== -1) {
        setOpenParentSection(parentIdx);
        localStorage.setItem('openParentSection', parentIdx);
        }
    } else if (storedParentSection !== null) {
        setOpenParentSection(parseInt(storedParentSection, 10));
    }
    }, []);

    const toggleParentSection = (idx, routeName) => {
      //alert(routeName);
    const newVal = openParentSection === idx ? null : idx;
    setOpenParentSection(newVal);
    localStorage.setItem('openParentSection', newVal);
    router.get(route(routeName), {}, {
      preserveScroll: true,
      preserveState: true,
    });
    };

    const toggleSites = () => {
    const newVal = !openSites;
    setOpenSites(newVal);
    localStorage.setItem('openSites', newVal);
    };

const handleNavigation = (routeName, params = {}) => {
  try {
    const resolvedRoute = params && Object.keys(params).length > 0
      ? route(routeName, params)
      : route(routeName);

    router.get(resolvedRoute, {}, {
      preserveScroll: true,
      preserveState: true,
    });
  } catch (error) {
    console.error('Navigation Error:', error);
  }
};


  const handleLogout = () => {
    router.post(route('logout'));
};




  return (
    <>
    {auth?.permissions?.includes('site_panel_show') && (
        <aside className="bg-[#25252C] pl-12 pt-4 pb-4 sm:pt-10 w-full lg:w-64 lg:min-h-[calc(100vh-64px-48px)]">
        <h2 className="sidebar-header">Choose Site</h2>
        {auth.teams.map((team) => (
            <ul key={team.id}  className="pt-6 space-y-2">
            <   li className=" hover:bg-gray-700 cursor-pointer">
                <Link
                href={route('sites.change', { site_id: team.id })}
                onClick={() => {
                    setSelectedSiteId(team.id);
                    localStorage.setItem('selectedSiteId', team.id);
                    setOpenParent('sites');
                    localStorage.setItem('openParent', 'sites');
                }}
                className={`block sidebar-links px-4 py-2 hover:bg-gray-700 ${
                    selectedSiteId == team.id ? 'text-yellow-400 font-bold' : ''
                }`}
                >
                {team.name}
                </Link>              
                </li>

            </ul>
        ))}
        <button className="flex pt-6 space-y-2 sidebar-links items-center py-2 hover:bg-gray-700"
            onClick={handleLogout}>
            Logout
        </button>
        </aside>
    )}
    {auth?.permissions?.includes('admin_panel_show') && (
        <aside className="bg-[#25252C] pl-12 pt-4 pb-4 sm:pt-10 w-full lg:w-64 lg:min-h-[calc(100vh-64px-48px)]">
        <>
            <h2 className="sidebar-header">Choose Site</h2>
            <ul className="space-y-2">
                <li>
                    <button
                    onClick={toggleSites}
                    className={`flex items-center px-4 py-2 w-full text-left hover:bg-gray-700
                        ${
                        openSites ? 'font-inter text-[16px] leading-[100%] tracking-[-0.28px] text-[#F6C546] font-bold' : 'sidebar-links'
                      }`}
                        
                    >
                    <i className="mr-3 fas fa-building" />
                    <span>Sites</span>
                    <i className={`ml-auto fas ${openSites ? 'fa-chevron-up' : 'fa-chevron-down'}`} />
                    </button>
                    {openSites && (
                        <ul className="pl-6 space-y-1">
                            {auth?.teams?.map((team) => (
                                <li key={team.id}>
                                <Link
                                href={route('sites.change', { site_id: team.id })}
                                onClick={() => {
                                    setSelectedSiteId(team.id);
                                    localStorage.setItem('selectedSiteId', team.id);
                                    setOpenSites(true); // keep sites open
                                    localStorage.setItem('openSites', true);
                                }}
                                className={`block  px-4 py-2 hover:bg-gray-700 ${selectedSiteId == team.id ? ' font-inter text-[16px] leading-[100%] tracking-[-0.28px] text-[#F6C546] font-bold' : 'sidebar-links'}`}
                                >
                                {team.name}
                                </Link>  
                                </li>
                            ))}
                        </ul>
                    )}
                </li>
            </ul>
        </>
        <h2 className="sidebar-header">Admin Panel</h2>
        <ul className="space-y-2">
{sidebarLinks.map((link, idx) => {
  if (link?.permission && !auth?.permissions?.includes(link.permission)) return null;

  return (
    <li key={idx}>
      {link.href ? (
        <a
          href={link.href}
          className="flex sidebar-links items-center px-4 py-2 hover:bg-gray-700"
          onClick={() => {
            setSelectedSubLink(link.href);
            localStorage.setItem('selectedSubLink', link.href);
            setOpenParentSection(null); // close dropdowns
            localStorage.setItem('openParentSection', null);
          }}
        >
          <i className={`mr-3 ${link.icon}`} />
          {link.label}
        </a>
      ) : link.dropdown ? (
        <>
          <button
            onClick={() => toggleParentSection(idx, link.route)} // 👈 Only toggles menu open/close
            className={`flex  items-center px-4 py-2 w-full text-left hover:bg-gray-700 ${
              openParentSection == idx ? 'font-inter text-[16px] leading-[100%] tracking-[-0.28px] text-[#F6C546] font-bold' : 'sidebar-links'
            }`}
          >
            <i className={`mr-3 ${link.icon}`} />
            <span>{link.label}</span>
            <i className={`ml-auto fas ${openParentSection === idx ? 'fa-chevron-up' : 'fa-chevron-down'}`} />
          </button>

          {openParentSection === idx && (
            <ul className="pl-6 space-y-1">
              {link.dropdown.map((sublink, subIdx) => {
                if (sublink?.permission && !auth?.permissions?.includes(sublink.permission)) return null;

                return (
                  <li key={subIdx}>
                    <button
                      onClick={() => {
                        setSelectedSubLink(sublink.route);
                        localStorage.setItem('selectedSubLink', sublink.route);

                        setOpenParentSection(idx); // keep parent open
                        localStorage.setItem('openParentSection', idx);

                        handleNavigation(sublink.route, sublink.param);
                      }}
                      className={`block  px-4 py-2 w-full text-left hover:bg-gray-700 ${
                        selectedSubLink === sublink.route ? 'font-inter text-[16px] leading-[100%] tracking-[-0.28px] text-[#F6C546] font-bold' : 'sidebar-links'
                      }`}
                    >
                      <i className={`mr-3 ${sublink.icon}`} />
                      {sublink.label}
                    </button>
                  </li>
                );
              })}
            </ul>
          )}
        </>
      ) : (
        // Handle flat links like "Dashboard"
        <Link
          href={route(link.route)}
          className={`flex sidebar-links items-center px-4 py-2 hover:bg-gray-700 ${
            selectedSubLink === link.route ? 'text-yellow-400 font-bold' : ''
          }`}
          onClick={() => {
            setSelectedSubLink(link.route);
            localStorage.setItem('selectedSubLink', link.route);
            setOpenParentSection(null); // close dropdowns
            localStorage.setItem('openParentSection', null);
          }}
        >
          <i className={`mr-3 ${link.icon}`} />
          {link.label}
        </Link>
      )}
    </li>
  );
})}

        </ul>
        <button className="flex sidebar-links items-center px-6 py-2 hover:bg-gray-700" onClick={handleLogout}>
            Logout
        </button>
        </aside>
    )}
    </>
    );
} 

export default Sidebar;

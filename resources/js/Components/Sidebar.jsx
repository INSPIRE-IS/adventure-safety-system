import React, { act, useState } from 'react';
import { Link, router, useForm, usePage } from '@inertiajs/react';


const Sidebar = () => {
  const { auth } = usePage().props;
  console.log('auth sidebar', auth);
  const [openParent, setOpenParent] = useState(null);

  const toggleParent = (idx) => {
    setOpenParent(openParent === idx ? null : idx); // Toggle clicked parent, close others
  };

//   const handleNavigation = (routeName, params = {}) => {
//     router.visit(route(routeName, params));
//   };

  const handleNavigation = (routeName, params = {}) => {
    console.log('routeName ', routeName);
    console.log('params ', params);
    // window.location.href = route(routeName, {
    //     data: params
    // });
        router.get(route(routeName, {data: params}));
    // router.visit(route(routeName), {
    //   method: 'get', // Or 'post' if needed
    //   data: params, // Pass parameters here
    //   preserveState: true, // Keeps state intact
    //   preserveScroll: true, // Keeps scroll position
    // });
  };

  const handleLogout = () => {
    router.post(route('logout'));
};


  const sidebarLinks = [
        {
            label: "Dashboard",
            icon: "fas fa-tachometer-alt",
            route: "dashboard",
            permission: null, // No permission required
        },
        {
            label: "Staff Management",
            icon: "fas fa-users",
            permission: "admin_panel_show",
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
                {
                    label: "Users",
                    icon: "fas fa-user",
                    route: "users.index",
                    permission: "user_access",
                },
                {
                    label: "Sites",
                    icon: "fas fa-building",
                    route: "sites.index",
                    permission: "site_access",
                },              
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

        {
            label: "Inspection Management",
            icon: "fas fa-users",
            permission: "admin_panel_show",
            dropdown: [
                {
                    label: "Type Cerfitications",
                    icon: "fas fa-file-signature",
                    route: "type-bools.index",
                    permission: "type_bool_access",
                },
                {
                    label: "Type Inspections",
                    icon: "fas fa-file-signature",
                    route: "type-inspections.index",
                    permission: "type_inspection_access",
                },
                {
                    label: "Element Items",
                    icon: "fas fa-user",
                    route: "category-items.index",
                    param: 1,
                    permission: "category_item_access",
                },
                {
                    label: "Element Details",
                    icon: "fas fa-bell",
                    route: "category-details.index",
                    param: 1,
                    permission: "category_detail_access",
                },
                {
                    label: "Inspections",
                    icon: "fas fa-building",
                    route: "inspections.index",
                    permission: "inspection_access",
                },
                {
                    label: "Inspection Schedule",
                    icon: "fas fa-building",
                    route: "inspection-schedules.index",
                    param: 1,
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
            label: "PPE Management",
            icon: "fas fa-users",
            permission: "admin_panel_show",
            dropdown: [
                // { // this is working it is not implemented on app and on ppe report though and was not part of original scope
                //     label: "Type Degradation",
                //     icon: "fas fa-unlock-alt",
                //     route: "type-degredations.index",
                //     permission: "type_degradation_access",
                // },
                {
                    label: "Equipment Items",
                    icon: "fas fa-user",
                    route: "category-items.index",
                    param: 3,
                    permission: "category_item_access",
                },
                {
                    label: "Equipment Details",
                    icon: "fas fa-bell",
                    route: "category-details.index",
                    param: 3,
                    permission: "category_detail_access",
                },
                {
                    label: "Inspections",
                    icon: "fas fa-building",
                    route: "equipments.index",
                    permission: "inspection_access",
                },
                {
                    label: "Inspection Schedule",
                    icon: "fas fa-building",
                    route: "inspection-schedules.index",
                    param: 3,
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
            label: "Safety Management",
            icon: "fas fa-users",
            permission: "admin_panel_show",
            dropdown: [
                {
                    label: "Type Drills",
                    icon: "fas fa-unlock-alt",
                    route: "type-drills.index",
                    permission: "type_drill_access",
                },
                {
                    label: "Drill Schedule",
                    icon: "fas fa-briefcase",
                    route: "drill-schedules.index",
                    permission: "drill_schedule_access",
                },            
                {
                    label: "Drills",
                    icon: "fas fa-file-alt",
                    route: "drills.index",
                    permission: "drill_access",
                },  
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
            label: "Incident Management",
            icon: "fas fa-users",
            permission: "admin_panel_show",
            dropdown: [
                {
                    label: "Incident Type",
                    icon: "fas fa-unlock-alt",
                    route: "type-incidents.index",
                    permission: "type_incident_access",
                },
                {
                    label: "Incident",
                    icon: "fas fa-briefcase",
                    route: "incidents.index",
                    permission: "incident_access",
                },
                {
                    label: "Question Types",
                    icon: "fas fa-bell",
                    route: "type-questions.index",
                    permission: "type_question_access",
                },
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
            label: "Files Management",
            icon: "fas fa-users",
            permission: "admin_panel_show",
            dropdown: [
                {
                    label: "Type Files",
                    icon: "fas fa-unlock-alt",
                    route: "type-files.index",
                    permission: "type_file_access",
                },
                {
                    label: "Files",
                    icon: "fas fa-briefcase",
                    route: "files.index",
                    permission: "file_access",
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

  return (
    <>
    {auth?.permissions?.includes('site_panel_show') && (
        <aside className="bg-[#25252C] pl-12 pt-4 pb-4 sm:pt-10 w-full lg:w-64 lg:min-h-[calc(100vh-64px-48px)]">
        <h2 className="sidebar-header">Choose Site</h2>
        {auth.teams.map((team) => (
            <ul key={team.id}  className="pt-6 space-y-2">
            <   li className=" hover:bg-gray-700 cursor-pointer">
                    <Link
                        href={route('sites.change',{site_id: team.id})}
                        className="block sidebar-links  "
                    >
                        {/* <i className={`mr-3 ${sublink?.icon}`} /> */}
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
                        onClick={() => toggleParent('sites')}
                        className="flex sidebar-links items-center px-4 py-2 w-full text-left hover:bg-gray-700"
                    >
                        <i className="mr-3" />
                        <span>Sites</span>
                        <i className={`ml-auto fas ${openParent === 'sites' ? 'fa-chevron-up' : 'fa-chevron-down'}`} />
                    </button>
                    {openParent === 'sites' && (
                        <ul className="pl-6 space-y-1">
                            {auth?.teams?.map((team) => (
                                <li key={team.id}>
                                    <Link
                                        href={route('sites.change', { site_id: team.id })}
                                        className="block sidebar-links px-4 py-2 hover:bg-gray-700"
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
            if (link?.permission && !auth?.permissions?.includes(link.permission)) {
            return null;
            }

            return (
            <li key={idx}>
                {link.dropdown ? (
                <>
                    <button
                    onClick={() => toggleParent(idx)}
                    className="flex sidebar-links items-center px-4 py-2 w-full text-left hover:bg-gray-700"
                    >
                    <i className={`mr-3 ${link.icon}`} />
                    <span>{link.label}</span>
                    <i className={`ml-auto fas ${openParent === idx ? "fa-chevron-up" : "fa-chevron-down"}`} />
                    </button>
                    {openParent === idx && (
                    <ul className="pl-6 space-y-1">
                        {link.dropdown.map((sublink, subIdx) => {
                            //alert(JSON.stringify(sublink));
                            if (sublink?.permission && !auth?.permissions?.includes(sublink.permission)) {
                                return null;
                            }
                        return (
                        <li key={subIdx}>
                        <button
                          onClick={() => handleNavigation(sublink.route, sublink.param)}
                          className="block sidebar-links px-4 py-2 w-full text-left hover:bg-gray-700"
                        >
                          <i className={`mr-3 ${sublink.icon}`} />
                          {sublink.label}
                        </button>
                        </li>
                        )
                        })}
                    </ul>
                    )}
                </>
                ) : (
                <Link
                    href={route(link.route)}
                    className="flex sidebar-links items-center px-4 py-2 hover:bg-gray-700"
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
import React, { act } from 'react';
import { Link, router, useForm, usePage } from '@inertiajs/react';


const Sidebar = () => {
  const { auth } = usePage().props;
  console.log('auth sidebar', auth);
  const { data, setData, post, processing, errors, reset } = useForm();

  const handleSubmit = (e) => {
    e.preventDefault();
    //alert('logout');
    document.getElementById('logoutform').submit();
    //post(route("logout"));

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
        label: "Equipment Management",
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
                label: "Category Items",
                icon: "fas fa-user",
                route: "category-items.index",
                permission: "category_item_access",
            },
            {
                label: "Category Details",
                icon: "fas fa-bell",
                route: "category-details.index",
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
                permission: "inspection_schedule_access",
            },
            // {
            //     label: "Inspection User Assigned",
            //     icon: "fas fa-building",
            //     route: "inspection-user-assigneds.index",
            //     permission: "inspection_user_assigned_access",
            // },
            {
                label: "Safety Inspection Details",
                icon: "fas fa-file-alt",
                route: "safety-inspection-details.index",
                permission: "safety_inspection_detail_access",
            },
            {
                label: "Equipment Inspection Details",
                icon: "fas fa-building",
                route: "equipment-inspection-details.index",
                permission: "equipment_inspection_detail_access",
            },
        ],
    },
    {
        label: "Drill Management",
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
            {
                label: "Drill Details",
                icon: "fas fa-file-alt",
                route: "drill-details.index",
                permission: "drill_detail_access",
            },                       
            {
                label: "Drill Members",
                icon: "fas fa-bell",
                route: "drill-members.index",
                permission: "drill_member_access",
            },            


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
            {
                label: "Post Incident Detail",
                icon: "fas fa-file-alt",
                route: "post-incident-details.index",
                permission: "post_incident_detail_access",
            },
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
            onClick={handleSubmit}>
            Logout
        </button>
        </aside>
    )}
    {auth?.permissions?.includes('admin_panel_show') && (
        <aside className="bg-[#25252C] pl-12 pt-4 pb-4 sm:pt-10 w-full lg:w-64 lg:min-h-[calc(100vh-64px-48px)]">
            <h2 className="sidebar-header">Choose Site</h2>
            <ul className=" space-y-2">
                <li key='99' className="group">
                    <div className="flex sidebar-links items-center px-4 py-2 cursor-pointer hover:bg-gray-700">
                        <i className={`mr-3 `} />
                        <span>Sites</span>
                    </div>
                    <ul className="hidden group-hover:block pl-6 space-y-1">
                        {auth?.teams?.map((team) => (
                            <li key={team.id} >
                                
                                <Link
                                    href={route('sites.change',{site_id: team.id})}
                                    className="block sidebar-links px-4 py-2 hover:bg-gray-700"
                                >
                                    {/* <i className={`mr-3 ${sublink?.icon}`} /> */}
                                    {team.name}
                                </Link>
                            </li>



                        ))}
                    </ul>
                </li>
            </ul>


            <h2 className="sidebar-header">Admin Panel</h2>
            <ul className="space-y-2">
                {sidebarLinks.map((link, idx) => {
                    // Check permission if needed
                    if (link?.permission && !auth?.permissions?.includes(link.permission)) {
                        return null;
                    }

                    // Render dropdown if available
                    if (link.dropdown) {
                        return (
                            <li key={idx} className="group">
                                <div className="flex sidebar-links items-center px-4 py-2 cursor-pointer hover:bg-gray-700">
                                    <i className={`mr-3 ${link?.icon}`} />
                                    <span>{link?.label}</span>
                                </div>
                                <ul className="hidden group-hover:block pl-6 space-y-1">
                                    {link?.dropdown?.map((sublink, subIdx) => {
                                        if (
                                            sublink?.permission &&
                                            !auth?.permissions?.includes(sublink?.permission)
                                        ) {
                                            return null;
                                        }
                                        return (
                                            <li key={subIdx}>
                                                <Link
                                                    href={route(sublink?.route)}
                                                    className="block sidebar-links px-4 py-2 hover:bg-gray-700"
                                                >
                                                    <i className={`mr-3 ${sublink?.icon}`} />
                                                    {sublink?.label}
                                                </Link>
                                            </li>
                                        );
                                    })}
                                </ul>
                            </li>
                        );
                    }

                    // Render a single link
                    return (
                        <li key={idx}>
                            {link?.route ? (
                                <Link
                                    href={route(link?.route)}
                                    className="flex sidebar-links items-center px-4 py-2 hover:bg-gray-700"
                                >
                                    <i className={`mr-3 ${link?.icon}`} />
                                    {link?.label}
                                </Link>
                            ) : (
                                <button
                                    onClick={link?.action}
                                    className="flex sidebar-links items-center px-4 py-2 hover:bg-gray-700"
                                >
                                    <i className={`mr-3 ${link?.icon}`} />
                                    {link?.label}
                                </button>
                            )}
                        </li>
                    );
                })}
            </ul>
            <button className="flex sidebar-links items-center py-2 hover:bg-gray-700"
                onClick={handleSubmit}>
                Logout
            </button>
        </aside>
    )}
    </>
    );
} 

export default Sidebar;
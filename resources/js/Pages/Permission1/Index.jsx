import React, { useEffect, useState } from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link, useForm , usePage } from "@inertiajs/react";
import Pagination from '@/Components/Pagination';
import PermissionModal from './PermissionModal';

const PermissionsIndex = ({auth, permissions, can }) => {
    //const { auth } = usePage().props;
    console.log('auth', auth.user.roles[0].permissions);
    const [isModalOpen, setIsModalOpen] = useState(false);
    const [modalMode, setModalMode] = useState("create");
    const [selectedPermission, setSelectedPermission] = useState(null);

    const handleOpenModal = (mode, permission = null) => {
        console.log('mode ', mode);
        console.log('permission ', permission);
        setModalMode(mode);
        setSelectedPermission(permission);
        setIsModalOpen(true);
    };

    const handleCloseModal = () => {
        setIsModalOpen(false);
        setSelectedPermission(null);
    };

    const handleDelete = (id) => {
        if (confirm("Are you sure you want to delete this permission?")) {
            post(route("permissions.destroy", id), {
                _method: "DELETE",
            });
        }
    };

    return (
        <AuthenticatedLayout user={auth.user} is_admin={auth?.is_admin}>
            <Head title="Permissions" />
            <div className="container mx-auto py-6">
            {/* Header */}
            <header className="mb-6 flex items-center justify-between w-[95%]">
                <h1 className="text-3xl font-bold">Permissions</h1>
                <p className="text-gray-600">List of all permissions in the system</p>
                {can.create_permission && (
                    // <Link
                    //     href={route("permissions.create")}
                    //     className="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700"
                    // >
                    //     Add Permission
                    // </Link>
                    <button
                        onClick={() => handleOpenModal("create")}
                        className="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700"
                    >
                        Add Permission
                    </button>
                )}
            </header>

            {/* Permissions Table */}
            <div className="overflow-x-auto bg-white shadow-md rounded-lg">
                <table className="min-w-full divide-y divide-gray-200">
                    <thead className="bg-gray-50">
                        <tr>
                            <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                ID
                            </th>
                            <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Title
                            </th>
                            <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody className="bg-white divide-y divide-gray-200">
                        {permissions.data.map((permission) => (
                            <tr key={permission.id}>
                                <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {permission.id}
                                </td>
                                <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {permission.name}
                                </td>
                                <td className="px-6 py-4 whitespace-nowrap text-sm">
                                    {can.show_permission && (
                                        // <Link
                                        //     href={route(
                                        //         "permissions.show",
                                        //         permission.id
                                        //     )}
                                        //     className="text-indigo-600 hover:text-indigo-900 mr-2"
                                        // >
                                        //     View
                                        // </Link>
                                        <button
                                            onClick={() =>
                                                handleOpenModal(
                                                    "view",
                                                    permission
                                                )
                                            }
                                            className="text-indigo-600 hover:text-indigo-900"
                                        >
                                            View
                                        </button>                                        
                                    )}
                                    {can.edit_permission && (
                                        // <Link
                                        //     href={route(
                                        //         "permissions.edit",
                                        //         permission.id
                                        //     )}
                                        //     className="text-blue-600 hover:text-blue-900 mr-2"
                                        // >
                                        //     Edit
                                        // </Link>
                                        <button
                                            onClick={() =>
                                                handleOpenModal(
                                                    "edit",
                                                    permission
                                                )
                                            }
                                            className="text-blue-600 hover:text-blue-900"
                                        >
                                            Edit
                                        </button>
                                    )}
                                    {can.delete_permission && (
                                        <button
                                            onClick={() =>
                                                handleDelete(permission.id)
                                            }
                                            className="text-red-600 hover:text-red-900"
                                        >
                                            Delete
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
                <Pagination links={permissions.links} />
            </div>            
            {/* Modal */}
            <PermissionModal
                isOpen={isModalOpen}
                onClose={handleCloseModal}
                mode={modalMode}
                permission={selectedPermission}
            />
            </div>

        </AuthenticatedLayout>
    );
};

export default PermissionsIndex;



// const PermissionModal = ({ isOpen, onClose, mode, permission = null }) => {
//     console.log('permission m ', permission);
//     const { data, setData, post, put, errors, processing, reset } = useForm({
//         name: "",
//     });

//     // Update the form data when the modal opens or permission changes
//     useEffect(() => {
//         if (permission) {
//             setData("name", permission.name || "");
//         } else {
//             reset(); // Clear form data when creating a new permission
//         }
//     }, [permission, setData, reset]);
//     console.log('data m ', data);
//     const handleSubmit = (e) => {
//         e.preventDefault();
//         if (mode === "create") {
//             post(route("admin.permissions.store"), {
//                 onSuccess: () => onClose(),
//             });
//         } else if (mode === "edit") {
//             put(route("admin.permissions.update", permission.id), {
//                 onSuccess: () => onClose(),
//             });
//         }
//     };

//     if (!isOpen) return null;

//     return (
//         <div className="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-50">
//             <div className="bg-white rounded-lg shadow-lg p-6 w-full max-w-md">
//                 <h2 className="text-xl font-bold mb-4">
//                     {mode === "create" && "Create Permission"}
//                     {mode === "edit" && "Edit Permission"}
//                     {mode === "view" && "View Permission"}
//                 </h2>
//                 <form onSubmit={handleSubmit}>
//                     <div className="mb-4">
//                         <label
//                             htmlFor="name"
//                             className="block text-sm font-medium text-gray-700"
//                         >
//                             Name
//                         </label>
//                         <input
//                             type="text"
//                             id="name"
//                             name="name"
//                             value={data.name}
//                             onChange={(e) => setData("name", e.target.value)}
//                             className={`mt-1 block w-full ${
//                                 errors.name ? "border-red-500" : "border-gray-300"
//                             } shadow-sm sm:text-sm rounded-md`}
//                             disabled={mode === "view"}
//                             required
//                         />
//                         {errors.name && (
//                             <p className="text-red-500 text-sm mt-2">
//                                 {errors.name}
//                             </p>
//                         )}
//                     </div>
//                     <div className="flex justify-end space-x-2">
//                         <button
//                             type="button"
//                             onClick={onClose}
//                             className="px-4 py-2 bg-gray-300 text-gray-800 rounded hover:bg-gray-400"
//                         >
//                             Cancel
//                         </button>
//                         {mode !== "view" && (
//                             <button
//                                 type="submit"
//                                 className="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700"
//                                 disabled={processing}
//                             >
//                                 {mode === "create" ? "Create" : "Update"}
//                             </button>
//                         )}
//                     </div>
//                 </form>
//             </div>
//         </div>
//     );
// };

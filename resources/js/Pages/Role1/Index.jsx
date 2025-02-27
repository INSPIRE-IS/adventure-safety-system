import React, { useState, useEffect } from "react";
import { Head, Link, usePage, useForm} from "@inertiajs/react";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import Pagination from "@/Components/Pagination";
import RoleModal from "./RoleModal";


const RolesIndex = ({ roles, permissions, can }) => {
    const { auth } = usePage().props;
    const [isModalOpen, setIsModalOpen] = useState(false);
    const [modalMode, setModalMode] = useState("create");
    const [selectedRole, setSelectedRole] = useState(null);

    const handleOpenModal = (mode, role = null) => {
        setModalMode(mode);
        setSelectedRole(role);
        setIsModalOpen(true);
    };

    const handleCloseModal = () => {
        setIsModalOpen(false);
        setSelectedRole(null);
    };

    const handleDelete = (id) => {
        if (confirm("Are you sure you want to delete this role?")) {
            Inertia.post(route("admin.roles.destroy", id), {
                _method: "DELETE",
            });
        }
    };

    return (
        <AuthenticatedLayout user={auth.user}>
            <Head title="Roles" />
            <div className="container mx-auto py-6">
                {/* Header */}
                <header className="mb-6 flex items-center justify-between w-[98%]">
                    <h1 className="text-3xl font-bold">Roles</h1>
                    {can.create_role && (
                        <button
                            onClick={() => handleOpenModal("create")}
                            className="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700"
                        >
                            Add Role
                        </button>
                    )}
                </header>

                {/* Roles Table */}
                <div className="overflow-x-auto bg-white shadow-md rounded-lg w-[98%]">
                    <table className="min-w-full divide-y divide-gray-200 w-full">
                        <thead className="bg-gray-50">
                            <tr>
                                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/5" style={{ width: "10%" }}>
                                    ID
                                </th>
                                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/5" style={{ width: "20%" }}>
                                    Title
                                </th>
                                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-2/5" style={{ width: "50%" }}>
                                    Permissions
                                </th>
                                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/5" style={{ width: "20%" }}>
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody className="bg-white divide-y divide-gray-200">
                            {roles.data.map((role) => (
                                <tr key={role.id}>
                                    <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500 w-1/5" style={{ width: "20%" }}>
                                        {role.id}
                                    </td>
                                    <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900 w-1/5" style={{ width: "20%" }}>
                                        {role.name}
                                    </td>
                                    <td className="px-6 py-4 whitespace-normal text-sm w-2/5" style={{ width: "40%" }}>
                                    <div
                                        className="flex flex-wrap gap-2 max-h-40 overflow-y-scroll"
                                        
                                    >
                                        {role.permissions.map((permission) => (
                                            <span
                                                key={permission.id}
                                                className="bg-blue-100 text-blue-800 text-xs font-medium mr-2 px-2.5 py-0.5 rounded"
                                            >
                                                {permission.name}
                                            </span>
                                        ))}
                                        </div>
                                    </td>
                                    <td className="px-6 py-4 whitespace-nowrap text-sm space-x-2 w-1/5" style={{ width: "20%" }}>
                                        {can.show_role && (
                                            <button
                                                onClick={() =>
                                                    handleOpenModal(
                                                        "view",
                                                        role
                                                    )
                                                }
                                                className="text-indigo-600 hover:text-indigo-900"
                                            >
                                                View
                                            </button>
                                        )}
                                        {can.edit_role && (
                                            <button
                                                onClick={() =>
                                                    handleOpenModal(
                                                        "edit",
                                                        role
                                                    )
                                                }
                                                className="text-blue-600 hover:text-blue-900"
                                            >
                                                Edit
                                            </button>
                                        )}
                                        {can.delete_role && (
                                            <button
                                                onClick={() =>
                                                    handleDelete(role.id)
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

                {/* Pagination */}
                <div className="mt-4">
                    <Pagination links={roles.links} />
                </div>

                {/* Modal */}
                <RoleModal
                    isOpen={isModalOpen}
                    onClose={handleCloseModal}
                    mode={modalMode}
                    role={selectedRole}
                    permissions={permissions}
                />
            </div>
        </AuthenticatedLayout>
    );
};

export default RolesIndex;





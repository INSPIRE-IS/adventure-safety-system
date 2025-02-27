import React, { useState, useEffect } from "react";
import { Head, Link, usePage, useForm} from "@inertiajs/react";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import Pagination from "@/Components/Pagination";
import SiteModal from "./SiteModal";


const SitesIndex = ({auth, sites, permissions, can }) => {
    //const { auth } = usePage().props;
    const [isModalOpen, setIsModalOpen] = useState(false);
    const [modalMode, setModalMode] = useState("create");
    const [selectedSite, setSelectedSite] = useState(null);

    const handleOpenModal = (mode, site = null) => {
        setModalMode(mode);
        setSelectedSite(site);
        setIsModalOpen(true);
    };

    const handleCloseModal = () => {
        setIsModalOpen(false);
        setSelectedSite(null);
    };

    const handleDelete = (id) => {
        if (confirm("Are you sure you want to delete this site?")) {
            Inertia.post(route("admin.sites.destroy", id), {
                _method: "DELETE",
            });
        }
    };

    return (
        <AuthenticatedLayout user={auth.user}>
            <Head title="Sites" />
            <div className="container mx-auto py-6">
                {/* Header */}
                <header className="mb-6 flex items-center justify-between w-[98%]">
                    <h1 className="text-3xl font-bold">Sites</h1>
                    {can.team_create && (
                        <button
                            onClick={() => handleOpenModal("create")}
                            className="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700"
                        >
                            Add Site
                        </button>
                    )}
                </header>

                {/* Sites Table */}
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
                                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/5" style={{ width: "20%" }}>
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody className="bg-white divide-y divide-gray-200">
                            {sites.data.map((site) => (
                                <tr key={site.id}>
                                    <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500 w-1/5" style={{ width: "20%" }}>
                                        {site.id}
                                    </td>
                                    <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900 w-1/5" style={{ width: "20%" }}>
                                        {site.name}
                                    </td>
                                    <td className="px-6 py-4 whitespace-nowrap text-sm space-x-2 w-1/5" style={{ width: "20%" }}>
                                        {can.team_show && (
                                            <button
                                                onClick={() =>
                                                    handleOpenModal(
                                                        "view",
                                                        site
                                                    )
                                                }
                                                className="text-indigo-600 hover:text-indigo-900"
                                            >
                                                View
                                            </button>
                                        )}
                                        {can.team_edit && (
                                            <button
                                                onClick={() =>
                                                    handleOpenModal(
                                                        "edit",
                                                        site
                                                    )
                                                }
                                                className="text-blue-600 hover:text-blue-900"
                                            >
                                                Edit
                                            </button>
                                        )}
                                        {can.team_delete && (
                                            <button
                                                onClick={() =>
                                                    handleDelete(site.id)
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
                    <Pagination links={sites.links} />
                </div>

                {/* Modal */}
                <SiteModal
                    isOpen={isModalOpen}
                    onClose={handleCloseModal}
                    mode={modalMode}
                    site={selectedSite}
                    permissions={permissions}
                />
            </div>
        </AuthenticatedLayout>
    );
};

export default SitesIndex;





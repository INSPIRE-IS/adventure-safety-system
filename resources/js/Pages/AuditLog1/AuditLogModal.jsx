import React, { useEffect } from "react";
import { useForm } from "@inertiajs/react";

const AuditLogModal = ({ isOpen, onClose, mode, auditLog = null }) => {
    console.log("auditLog", auditLog);
    const { data, setData, post, put, errors, reset, processing } = useForm({
        id: "",
        description: "",
        subject_id: "",
        subject_type: "",
        user_id: "",
        host: "",
        created_at: "",
        properties: [],
    });
    useEffect(() => {
        if (auditLog && mode !== "create") {
            setData({
                id: auditLog.id || "",
                description: auditLog.description || "",
                subject_id: auditLog.subject_id || "",
                subject_type: auditLog.subject_type || "",
                user_id: auditLog.user_id || "",
                host: auditLog.host || "",
                created_at: auditLog.created_at || "",
                properties: auditLog.properties || [],
            });
        } else if (mode === "create") {
            reset(); // Clear form for create mode
        }
    }, [mode,auditLog]);

    const handleChange = (name, value) => {
        setData(name, value);
        //setData("name", e.target.value);
    };

    const handleSubmit = (e) => {
        e.preventDefault();
        if (mode === "create") {
            post(route("audit-logs.store"), {
                onSuccess: () => onClose(),
            });
        } else if (mode === "edit") {
            put(route("audit-logs.update", auditLog.id), {
                onSuccess: () => onClose(),
            });
        }
    };

    if (!isOpen) return null;

    return (
        <div className="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-50">
            <div className="bg-white rounded-lg shadow-lg p-6 w-full max-w-md">
                <h2 className="text-xl font-bold mb-4">
                    {mode === "create" && "Create AuditLog"}
                    {mode === "edit" && "Edit AuditLog"}
                    {mode === "view" && "View AuditLog"}
                </h2>
                <form onSubmit={handleSubmit}>
                    <div className="mb-4">
                        <label
                            htmlFor="description"
                            className="block text-sm font-medium text-gray-700"
                        >
                            <strong>Description:</strong> {auditLog.description}
                        </label>
                    </div>
                    <div className="mb-4">
                        <label
                            htmlFor="subject_id"
                            className="block text-sm font-medium text-gray-700"
                        >
                            <strong>subject_id:</strong> {auditLog.subject_id}
                        </label>
                    </div>
                    <div className="mb-4">
                        <label
                            htmlFor="subject_type"
                            className="block text-sm font-medium text-gray-700"
                        >
                            <strong>subject_type:</strong> {auditLog.subject_type}
                        </label>
                    </div>
                    <div className="mb-4">
                        <label
                            htmlFor="user_id"
                            className="block text-sm font-medium text-gray-700"
                        >
                            <strong>user_id:</strong> {auditLog.user_id}
                        </label>
                    </div>
                    <div className="mb-4">
                        <label
                            htmlFor="host"
                            className="block text-sm font-medium text-gray-700"
                        >
                            <strong>host:</strong> {auditLog.host}
                        </label>
                    </div>
                    <div className="mb-4">
                        <label
                            htmlFor="created_at"
                            className="block text-sm font-medium text-gray-700"
                        >
                            <strong>created_at:</strong> {auditLog.created_at}
                        </label>
                    </div>
                    <div className="mb-4">
                        <label
                            htmlFor="properties"
                            className="block text-sm font-medium text-gray-700"
                        >
                            properties
                        </label>
                        <div className="mt-1 relative rounded-md shadow-sm">
                        {Object.entries(auditLog.properties).map(([key, value]) => (
                                        <div key={key}>
                                            <strong>{key}:</strong> {value}
                                        </div>
                                    ))}
                        </div>
                    </div>
                    <div className="flex justify-end space-x-2">
                        <button
                            type="button"
                            onClick={onClose}
                            className="px-4 py-2 bg-gray-300 text-gray-800 rounded hover:bg-gray-400"
                        >
                            Close
                        </button>
                    </div>
                </form>
            </div>
        </div>
    );
};

export default AuditLogModal;

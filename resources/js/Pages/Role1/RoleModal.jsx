import { useForm } from "@inertiajs/react";
import { useEffect } from "react";


const RoleModal = ({ isOpen, onClose, mode, role = null, permissions }) => {
    console.log('role', role);
    console.log('permissions', permissions);
    const { data, setData, post, put, errors, reset, processing } = useForm({
        name: "",
        permissions: [],
    });
    
    useEffect(() => {
        if (role && mode !== "create") {
            setData({
                name: role.name || "",
                permissions: role.permissions.map((perm) => perm.id),
            });
        } else if (mode === "create") {
            reset(); // Clear form for create mode
        }
    }, [mode,role]);



    // Handle permission checkbox changes
    const handlePermissionChange = (permissionId, isChecked) => {
        setData("permissions", (prevPermissions) => {
            if (!Array.isArray(prevPermissions)) {
                prevPermissions = [];
            }

            const updatedPermissions = isChecked
                ? [...prevPermissions, permissionId] // Add permission
                : prevPermissions.filter((id) => id !== permissionId); // Remove permission

            return updatedPermissions;
        });
    };

    // Check if a permission is selected
    // const isChecked = (permissionId) => {
    //     return Array.isArray(data.permissions) && data.permissions.includes(permissionId);
    // };

    // Handle form submission
    const handleSubmit = (e) => {
        e.preventDefault();
        if (mode === "create") {
            post(route("roles.store"), {
                onSuccess: () => onClose(),
            });
        } else if (mode === "edit") {
            put(route("roles.update", role.id), {
                onSuccess: () => onClose(),
            });
        }
    };

    if (!isOpen) return null;

    return (
        <div className="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-50">
            <div className="bg-white rounded-lg shadow-lg p-6 w-full max-w-md">
                <h2 className="text-xl font-bold mb-4">
                    {mode === "create" && "Create Role"}
                    {mode === "edit" && "Edit Role"}
                    {mode === "view" && "View Role"}
                </h2>
                <form onSubmit={handleSubmit}>
                    <div className="mb-4">
                        <label
                            htmlFor="name"
                            className="block text-sm font-medium text-gray-700"
                        >
                            Name
                        </label>
                        <input
                            type="text"
                            id="name"
                            name="name"
                            value={data.name}
                            onChange={(e) => setData("name", e.target.value)}
                            className={`mt-1 block w-full ${
                                errors.name ? "border-red-500" : "border-gray-300"
                            } shadow-sm sm:text-sm rounded-md`}
                            disabled={mode === "view"}
                            required
                        />
                        {errors.name && (
                            <p className="text-red-500 text-sm mt-2">
                                {errors.name}
                            </p>
                        )}
                    </div>

                    {/* Permissions Selection */}
                    <div className="mb-4">
                        <label className="block text-sm font-medium text-gray-700">
                            Permissions
                        </label>
                        <div
                            className="mt-1 border border-gray-300 rounded-md p-2 max-h-80 w-full overflow-y-scroll flex flex-wrap gap-2"
                        >
                            {permissions.map((permission) => (
                                <label
                                    key={permission.id}
                                    className="flex items-center text-sm space-x-2"
                                >
                                    <input
                                        type="checkbox"
                                        value={permission.id}
                                        checked={Array.isArray(data.permissions) && data.permissions.includes(permission.id)}
                                        onChange={(e) => {
                                            const isChecked = e.target.checked;
                                            setData((prevState) => {
                                                const updatedPermissions = isChecked
                                                    ? [...prevState.permissions, permission.id]
                                                    : prevState.permissions.filter((id) => id !== permission.id);
                                            
                                                return { ...prevState, permissions: updatedPermissions };
                                            });
                                        }
                                        }
                                        className="form-checkbox h-4 w-4 text-blue-600"
                                        disabled={mode === "view"}
                                    />
                                    <span>{permission.name}</span>
                                </label>
                            ))}
                        </div>
                    </div>

                    <div className="flex justify-end space-x-2">
                        <button
                            type="button"
                            onClick={onClose}
                            className="px-4 py-2 bg-gray-300 text-gray-800 rounded hover:bg-gray-400"
                        >
                            Cancel
                        </button>
                        {mode !== "view" && (
                            <button
                                type="submit"
                                className="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700"
                                disabled={processing}
                            >
                                {mode === "create" ? "Create" : "Update"}
                            </button>
                        )}
                    </div>
                </form>
            </div>
        </div>
    );
};

export default RoleModal;
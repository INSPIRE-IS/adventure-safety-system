import React, { useEffect } from "react";
import { useForm } from "@inertiajs/react";
import InputText from "@/Components/InputText";
import MultiSelect from "@/Components/MultiSelect";

const RoleModal = ({ isOpen, onClose, mode, role = null, permissions }) => {
    //console.log("mode", mode);
    //console.log("role", role);
    //console.log("permissions", permissions);
    const { data, setData, post, put, errors, reset, processing } = useForm({
        name: "",
        permissions: [],
    });
    useEffect(() => {
        if (role && mode !== "create") {
            setData({
                name: role.name || "",
                permissions: role.permissions.map((permission) => permission.id),
            });
        } else if (mode === "create") {
            reset(); // Clear form for create mode
        }
    }, [mode,role]);

    const handleChange = (target, value) => {
        setData(target, value);
        //setData("name", e.target.value);
    };
    const handleIsChecked = (check_array, check_id) => {
        //console.log("check_array", check_array);
        //console.log("check_id", check_id);
        return Array.isArray(check_array) && check_array.includes(check_id);
    };
    const handleCheckChange = (event,check_id) => {
        //console.log("event", event);
        //console.log("check_id", check_id);
        const isChecked = event.target.checked;
        //console.log("isChecked", isChecked);
        setData((prevState) => {
            const updatedPermissions = isChecked
                ? [...prevState.permissions, check_id]
                : prevState.permissions.filter((id) => id !== check_id);
        
            return { ...prevState, permissions: updatedPermissions };
        });
    };

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
                    <InputText
                        title="Name"
                        field="name"
                        value={data.name}
                        mode={mode}
                        handleChange={handleChange}
                        errors={errors}
                    />
                    {/* <div className="mb-4">
                        <label className="block input-header">
                            Permissions
                        </label>
                        <div
                            className="mt-1 border border-gray-300 rounded-md p-2 max-h-80 w-full overflow-y-scroll flex flex-wrap gap-2"
                        >
                            {permissions?.map((permission) => (
                                <label
                                    key={permission.id}
                                    className="flex items-center  space-x-2"
                                >
                                    <input
                                        type="checkbox"
                                        value={permission.id}
                                        checked={handleIsChecked(data.permissions, permission.id)}
                                        onChange={(e) => handleCheckChange(e, permission.id)}
                                        className="form-checkbox h-4 w-4 text-blue-600"
                                        disabled={mode === "view"}
                                    />
                                    <span className="input-text">{permission.name}</span>
                                </label>
                            ))}
                        </div>
                    </div> */}
                    <MultiSelect
                        title="Permissions"
                        fields={permissions}
                        allFields={data.permissions}
                        mode={mode}
                        handleIsChecked={handleIsChecked}
                        handleCheckChange={handleCheckChange}
                    />
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

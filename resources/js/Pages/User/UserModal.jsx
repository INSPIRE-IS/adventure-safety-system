import React, { useEffect } from "react";
import { useForm } from "@inertiajs/react";
import InputText from "@/Components/InputText";
import CustomCheckbox from "@/Components/CustomCheckbox";
import CustomSelect1 from "@/Components/CustomSelect1";

const UserModal = ({ isOpen, onClose, mode, user = null, roles, teams, team }) => {
    // console.log("mode", mode);
    // console.log("user", user);
    // console.log("roles", roles);
    const { data, setData, post, put, errors, reset, processing } = useForm({
        name: "",
        surname: "",
        email: "",
        password: "",
        mobile_number: "",
        roles: [],
        teams: [team?.id],
        is_admin: false,
        is_email_recipient: false,
    });
    useEffect(() => {
        if (user && mode !== "create") {
            setData({
                name: user.name || "",
                surname: user.surname || "",
                email: user.email || "",
                password: "",
                mobile_number: user.mobile_number || "",
                roles: user.roles.map((role) => role.id),
                teams: user.teams.map((team) => team.id),
                is_admin: user.is_admin,
                is_email_recipient: user.is_email_recipient,
            });
        } else if (mode === "create") {
            reset(); // Clear form for create mode
        }
    }, [mode,user,team]);

    const handleChange = (target, value) => {
        setData(target, value);
        //setData("name", e.target.value);
    };

    const handleSubmit = (e) => {
        e.preventDefault();
        if (mode === "create") {
            post(route("users.store"), {
                onSuccess: () => onClose(),
            });
        } else if (mode === "edit") {
            put(route("users.update", user.id), {
                onSuccess: () => onClose(),
            });
        }
    };

    if (!isOpen) return null;

    return (
        <div className="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-50">
            <div className="bg-white rounded-lg shadow-lg p-6 w-full max-w-md">
                <h2 className="text-xl font-bold mb-4">
                    {mode === "create" && "Create User"}
                    {mode === "edit" && "Edit User"}
                    {mode === "view" && "View User"}
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
                    <InputText
                        title="Surname"
                        field="surname"
                        value={data.surname}
                        mode={mode}
                        handleChange={handleChange}
                        errors={errors}
                    />
                    <InputText
                        title="Email"
                        field="email"
                        value={data.email}
                        mode={mode}
                        handleChange={handleChange}
                        errors={errors}
                    />
                    <InputText
                        title="Password"
                        field="password"
                        value={data.password}
                        mode={mode}
                        handleChange={handleChange}
                        errors={errors}
                    />
                    <InputText
                        title="Mobile Number"
                        field="mobile_number"
                        value={data.mobile_number}
                        mode={mode}
                        handleChange={handleChange}
                        errors={errors}
                    />
                    <CustomSelect1
                        label="roles"
                        name="roles"
                        value={data.roles}
                        mode={mode}
                        onChange={(e) => handleChange(e.target.name, e.target.value)}
                        error={errors.roles}
                        options={roles}
                    />
                    {/* <div className="mb-4">
                        <label className="block text-sm font-medium text-gray-700">
                            Roles
                        </label>
                        <div
                            className="mt-1 border border-gray-300 rounded-md p-2 max-h-80 w-full overflow-y-scroll flex flex-wrap gap-2"
                        >
                            {roles?.map((role) => (
                                <label
                                    key={role.id}
                                    className="flex items-center text-sm space-x-2"
                                >
                                    <input
                                        type="checkbox"
                                        value={role.id}
                                        checked={Array.isArray(data.roles) && data.roles.includes(role.id)}
                                        onChange={(e) => {
                                            const isChecked = e.target.checked;
                                            setData((prevState) => {
                                                const updatedRoles = isChecked
                                                    ? [...prevState.roles, role.id]
                                                    : prevState.roles.filter((id) => id !== role.id);
                                            
                                                return { ...prevState, roles: updatedRoles };
                                            });
                                            }
                                        }
                                        className="form-checkbox h-4 w-4 text-blue-600"
                                        disabled={mode === "view"}
                                    />
                                    <span>{role.name}</span>
                                </label>
                            ))}
                        </div>
                    </div> */}
                    {/* <div className="mb-4">
                        <label className="block text-sm font-medium text-gray-700">
                            Teams
                        </label>
                        <div
                            className="mt-1 border border-gray-300 rounded-md p-2 max-h-80 w-full overflow-y-scroll flex flex-wrap gap-2"
                        >
                            {teams?.map((team) => (
                                <label
                                    key={team.id}
                                    className="flex items-center text-sm space-x-2"
                                >
                                    <input
                                        type="checkbox"
                                        value={team.id}
                                        checked={Array.isArray(data.teams) && data.teams.includes(team.id)}
                                        onChange={(e) => {
                                            const isChecked = e.target.checked;
                                            setData((prevState) => {
                                                const updatedTeams = isChecked
                                                    ? [...prevState.teams, team.id]
                                                    : prevState.teams.filter((id) => id !== team.id);
                                            
                                                return { ...prevState, teams: updatedTeams };
                                            });
                                            }
                                        }
                                        className="form-checkbox h-4 w-4 text-blue-600"
                                        disabled={mode === "view"}
                                    />
                                    <span>{team.name}</span>
                                </label>
                            ))}
                        </div>
                    </div> */}
                    {/* <CustomCheckbox
                        label="Is Admin"
                        name="is_admin"
                        checked={data.is_admin}
                        mode={mode}
                        onChange={handleChange}
                        errors={errors}
                    /> */}
                    {/* <CustomCheckbox
                        label="Is Email Recipient"
                        name="is_email_recipient"
                        checked={data.is_email_recipient}
                        mode={mode}
                        onChange={handleChange}
                        errors={errors}
                    /> */}
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

export default UserModal;

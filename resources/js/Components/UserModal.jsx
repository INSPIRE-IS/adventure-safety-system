import React, { useEffect } from "react";
import { useForm } from "@inertiajs/react";
import InputText from "./InputText";


const UserModal = ({ isOpen, onClose, user = null }) => {

    console.log("user", user);

    const { data, setData, put, errors, reset, processing } = useForm({
        name: "",
        email: "",
        password: "",
        roles: [],
        teams: [],
    });
    useEffect(() => {
            setData({
                name: user.name || "",
                email: user.email || "",
                password: user.password || "",
                roles: user.roles || [],
                teams: user.teams || [],
            });
    }, [user]);

    const handleChange = (target, value) => {
        setData(target, value);
        //setData("name", e.target.value);
    };

    const handleSubmit = (e) => {
        e.preventDefault();

        put(route("type-drills.update", user.id), {
            onSuccess: () => {reset();onClose()},
        });

    };

    if (!isOpen) return null;

    return (
        <div className="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-50">
            <div className="bg-white rounded-lg shadow-lg p-6 w-full max-w-md">
                <h2 className="text-xl font-bold mb-4">
                    User

                </h2>
                <form onSubmit={handleSubmit}>
                    <InputText
                        title="Name"
                        field="name"
                        value={data.name}
                        mode={"view"}
                        handleChange={handleChange}
                        errors={errors}
                    />
                    <InputText
                        title="Email"
                        field="email"
                        value={data.email}
                        mode={"view"}
                        handleChange={handleChange}
                        errors={errors}
                        type="email"
                    />
                    {/* <InputText
                        title="Password"
                        field="password"
                        value={data.password}
                        mode={"create"}
                        handleChange={handleChange}
                        errors={errors}
                    /> */}
                    <label
                        htmlFor="teams"
                        className="block input-header"
                    >Role/s</label>
                    {data?.roles && data?.roles.length > 0 && (
                        <div className="flex flex-col space-y-2 mb-2">
                            {data?.roles?.map((role) => (
                                <div key={role.id} className="flex flex-row space-x-2 ml-4">
                                    <label htmlFor={role.id}>{role.name}</label>
                                </div>
                            ))}
                        </div>
                    )}
                    <label
                        htmlFor="teams"
                        className="block input-header"
                    >Team/s</label>
                    {data?.teams && data?.teams.length > 0 && (
                        <div className="flex flex-col space-y-2 mb-2">
                            {data?.teams?.map((team) => (
                                <div key={team.id} className="flex flex-row space-x-2 ml-4">
                                    <label htmlFor={team.id}>{team.name}</label>
                                </div>
                            ))}
                        </div>
                    )}

                    <div className="flex justify-end space-x-2">
                        <button
                            type="button"
                            onClick={onClose}
                            className="px-4 py-2 bg-gray-300 text-gray-800 rounded hover:bg-gray-400"
                        >
                            Cancel
                        </button>

                            <button
                                type="submit"
                                className="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700"
                                disabled={processing}
                            >
                               Update
                            </button>

                    </div>
                </form>
            </div>
        </div>
    );
};

export default UserModal;

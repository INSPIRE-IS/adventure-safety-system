import { useForm } from "@inertiajs/react";
import { useEffect } from "react";


const SiteModal = ({ isOpen, onClose, mode, site = null }) => {
    console.log('site', site);

    const { data, setData, post, put, errors, reset, processing } = useForm({
        name: "",
    });

    useEffect(() => {
        if (site && mode !== "create") {
            setData({
                name: site.name || "",
            });
        } else if (mode === "create") {
            reset(); // Clear form for create mode
        }
    }, [mode,site]);


    // Handle form submission
    const handleSubmit = (e) => {
        e.preventDefault();
        if (mode === "create") {
            post(route("sites.store"), {
                onSuccess: () => onClose(),
            });
        } else if (mode === "edit") {
            put(route("sites.update", site.id), {
                onSuccess: () => onClose(),
            });
        }
    };

    if (!isOpen) return null;

    return (
        <div className="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-50">
            <div className="bg-white rounded-lg shadow-lg p-6 w-full max-w-md">
                <h2 className="text-xl font-bold mb-4">
                    {mode === "create" && "Create Site"}
                    {mode === "edit" && "Edit Site"}
                    {mode === "view" && "View Site"}
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

export default SiteModal;
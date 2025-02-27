
const MultiSelect = ({ title, fields, allFields,mode, handleIsChecked,handleCheckChange }) => {
    return (
        <div className="mb-4">
        <label className="block input-header">
            {title}
        </label>
        <div
            className="mt-1 border border-gray-300 rounded-md p-2 max-h-80 w-full overflow-y-scroll flex flex-wrap gap-2"
        >
            {fields?.map((field) => (
                <label
                    key={field.id}
                    className="flex items-center space-x-2"
                >
                    <input
                        type="checkbox"
                        value={field.id}
                        checked={handleIsChecked(allFields, field.id)}
                        onChange={(e) => handleCheckChange(e, field.id)}
                        className="form-checkbox h-4 w-4 text-gray-500"
                        disabled={mode === "view"}
                    />
                    <span className="input-text">{field.name}</span>
                </label>
            ))}
        </div>
    </div>

    );
};

export default MultiSelect;

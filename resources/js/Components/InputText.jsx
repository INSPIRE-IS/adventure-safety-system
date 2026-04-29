const InputText = ({ title, field, value, mode, handleChange, errors, required = false, type = "text" }) => {
    const fieldError = errors?.[field];
    return (
        <div className="mb-4">
            <label
                htmlFor={field}
                className="block input-header"
            >
                {title}
            </label>
            <input
                type={type}
                id={field}
                name={field}
                value={value}
                onChange={(e) => handleChange(field, e.target.value)}
                className={`mt-1 block w-full ${fieldError ? "border-red-500" : "border-gray-300"} shadow-sm input-text rounded-xl`}
                disabled={mode === "view"}
                required={required}
            />
            {fieldError && (
                <p className="text-red-500 text-sm mt-2">
                    {fieldError}
                </p>
            )}
        </div>

    );
};

export default InputText;


const CustomButton = ({title, btnColor = "primary", hoverColor= "primary-dark", hoverTextColor="text-white", type="button", onClick }) => {
    return (
        <button
        type={type}
            className={`${btnColor} w-[160px] h-[34px] button-text1 rounded-xl hover:${hoverColor} hover:${hoverTextColor} hover:${hoverTextColor}`}
            onClick={onClick}
        >
                {title}
        </button>
    );
};
                            {/* <button
                                type="button"
                                onClick={handleAddIdentification}
                                className="p-2 bg-blue-500 text-white rounded hover:bg-blue-600"
                            >
                                Add
                            </button> */}
export default CustomButton;

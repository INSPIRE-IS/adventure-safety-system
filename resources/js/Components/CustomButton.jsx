
const CustomButton = ({title, btnColor = "primary", hoverColor= "primary-dark", hoverTextColor="text-white", onClick }) => {
    return (
        <button
            className={`${btnColor} w-[160px] h-[34px] button-text1 rounded-xl hover:${hoverColor} hover:${hoverTextColor} hover:${hoverTextColor}`}
            onClick={onClick}
        >
                {title}
        </button>
    );
};

export default CustomButton;

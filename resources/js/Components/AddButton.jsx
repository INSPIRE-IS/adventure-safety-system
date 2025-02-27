const AddButton = ({title,icon, onClick }) => {
    return (
        <button
            className="bg-[#F6C546] hover:bg-[#f8bf2d] px-4 py-2 w-[320px] h-[68px] rounded-xl"
            onClick={onClick}
        >
            <div className="w-full h-full flex flex-row items-center justify-between">
                <h4 className="button-text1">{title}</h4>
                <img src={icon} alt="User Icon" width={45} height={45} />
            </div>
        </button>
    );
};

export default AddButton;

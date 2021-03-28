<?php

namespace Digital\Fileuploadcustom\Controller\Index;

use Magento\Framework\App\Filesystem\DirectoryList as DirectoryList;
use \Magento\Framework\Controller\ResultFactory;

class Index extends \Magento\Framework\App\Action\Action {

    protected $_filesystem;
    protected $directory_list;
    protected $_messageManager;

    public function __construct(
            \Magento\Framework\App\Action\Context $context,
            \Magento\Framework\Filesystem $fileSystem,
            DirectoryList $directory_list,
            \Magento\Framework\Message\ManagerInterface $messageManager
    ) {
        $this->_filesystem = $fileSystem;
        $this->directory_list = $directory_list;
        $this->_messageManager = $messageManager;

        parent::__construct($context);
    }

    public function execute() {

        $this->_view->loadLayout();
        $this->_view->getLayout()->initMessages();
        $this->_view->renderLayout();

        $result = array();
        if ($_FILES['test_image']['name']) {


            $uploader = $this->_objectManager->create(
                    'Magento\MediaStorage\Model\File\Uploader',
                    ['fileId' => 'test_image']
            );
            $filetype = $_FILES['test_image']['name'];
            $filetype = explode(".", $filetype);
            $filetype = $filetype[1];

            if ($filetype == 'jpg' || $filetype == 'jpeg' || $filetype == 'gif' || $filetype == 'pdf') {
                    $uploader->setAllowRenameFiles(true);
                    $uploader->setFilesDispersion(true);
                    $mediaDirectory = $this->_filesystem->getDirectoryRead(DirectoryList::MEDIA);
                    $result = $uploader->save($mediaDirectory->getAbsolutePath('customimage'));
                    $this->_messageManager->addSuccessMessage('Upload success');
                    $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
                    $resultRedirect->setUrl($this->_redirect->getRefererUrl());

                    return $resultRedirect;
            } else {
                try {
                     $this->_messageManager->addError('Upload only jpg,jpeg,gif,png');
                     $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
                     $resultRedirect->setUrl($this->_redirect->getRefererUrl());
                    return $resultRedirect;
                } catch (Exception $e) {
                    
                }
            }
        }
    }

}

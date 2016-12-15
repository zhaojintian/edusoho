<?php
namespace Topxia\WebBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Topxia\Service\Common\ServiceKernel;

class LessonMaterialPluginController extends BaseController
{
    public function initAction(Request $request)
    {
        list($course, $member) = $this->getCourseService()->tryTakeCourse($request->query->get('courseId'));
        $lesson                = $this->getCourseService()->getCourseLesson($course['id'], $request->query->get('lessonId'));

        if ($lesson['mediaId'] > 0) {
            $file = $this->getUploadFileService()->getFile($lesson['mediaId']);
        } else {
            $file = null;
        }

        $lessonMaterials = $this->getMaterialService()->searchMaterials(
            array('lessonId' => $lesson['id'], 'source' => 'coursematerial', 'type' => 'course'),
            array('createdTime','DESC'), 
            0, 100
        );
        return $this->render('TopxiaWebBundle:LessonMaterialPlugin:index.html.twig',array(
            'materials' => $lessonMaterials,
            'course'    => $course,
            'lesson'    => $lesson,
            'file'      => $file
        ));
    }

    protected function getUploadFileService()
    {
        return ServiceKernel::instance()->getBiz()->service('File:UploadFileService');
    }

    protected function getCourseService()
    {
        return $this->getServiceKernel()->createService('Course.CourseService');
    }

    protected function getMaterialService()
    {
        return $this->getServiceKernel()->createService('Course.MaterialService');
    }
}

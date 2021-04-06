<?php


namespace App\Api\Controller\Threads;


use App\Models\Category;
use App\Models\Post;
use App\Models\Thread;
use Discuz\Auth\AssertPermissionTrait;
use Illuminate\Database\Eloquent\Builder;
use Psr\Http\Message\ServerRequestInterface;
use Tobscure\JsonApi\Document;

class ListDraftThreadController extends ListThreadsController
{
    use AssertPermissionTrait;

    protected function data(ServerRequestInterface $request, Document $document)
    {
        $actor = $request->getAttribute('actor');

        // 没有任何一个分类的查看权限时，判断是否有全局权限
        if (! Category::getIdsWhereCan($actor, 'viewThreads')) {
            $this->assertCan($actor, 'viewThreads');
        }

        $limit = $this->extractLimit($request);
        $filter = $this->extractFilter($request);
        $offset = $this->extractOffset($request);
        $include = $this->extractInclude($request);
        $sort = $this->extractSort($request);
        $params = $request->getQueryParams();
        $page = $params['page'];

        $threads = $this->search($actor, $filter, $sort, $limit, $offset, $page);

        $document->addPaginationLinks(
            $this->url->route('threads.index'),
            $request->getQueryParams(),
            $offset,
            $limit,
            $this->threadCount
        );

        $document->setMeta([
            'threadCount' => $this->threadCount,
            'pageCount' => ceil($this->threadCount / $limit),
        ]);

        Thread::setStateUser($actor, $threads);
        Post::setStateUser($actor);

        // 加载其他关联
        $threads->loadMissing($include);

        return $threads;
    }

    public function search($actor, $filter, $sort, $limit = null, $offset = 0, $page)
    {
        /** @var Builder $query */
        $query = $this->threads->query()->whereVisibleTo($actor);

        $query = $query->select('threads.*')
            ->join('posts', 'threads.id', '=', 'posts.thread_id')
            ->where('posts.is_first', true)
            ->where('is_draft',1)
            ->where('threads.user_id', $actor->id)
            ->whereNull('threads.deleted_at');

        $this->threadCount = $limit > 0 ? $query->count() : null;

        $query->skip($offset)->take($limit);

        $query->orderBy('threads.updated_at', 'desc');

        return $query->get();
    }
}

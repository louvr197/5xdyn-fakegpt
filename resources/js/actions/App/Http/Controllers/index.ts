import AskController from './AskController'
import ConversationController from './ConversationController'
import MessageController from './MessageController'
import UserModelController from './UserModelController'
import Settings from './Settings'
const Controllers = {
    AskController: Object.assign(AskController, AskController),
ConversationController: Object.assign(ConversationController, ConversationController),
MessageController: Object.assign(MessageController, MessageController),
UserModelController: Object.assign(UserModelController, UserModelController),
Settings: Object.assign(Settings, Settings),
}

export default Controllers